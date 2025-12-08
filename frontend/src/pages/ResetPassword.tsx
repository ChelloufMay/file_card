import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, useSearchParams, Link } from 'react-router-dom';
import { authService } from '../services/authService';
import Input from '../components/Input';
import Button from '../components/Button';
import Alert from '../components/Alert';

const ResetPassword: React.FC = () => {
  const navigate = useNavigate();
  const { token: tokenParam } = useParams<{ token: string }>();
  const [searchParams] = useSearchParams();
  const [formData, setFormData] = useState({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [alert, setAlert] = useState<{ type: 'success' | 'error'; message: string } | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const token = tokenParam || searchParams.get('token');
    const email = searchParams.get('email');
    
    if (token && email) {
      setFormData((prev) => ({ ...prev, token, email }));
    } else {
      setAlert({
        type: 'error',
        message: 'Invalid reset link. Please request a new one.',
      });
    }
  }, [tokenParam, searchParams]);

  const validatePassword = (password: string): string | null => {
    if (password.length < 6) {
      return 'Password must be at least 6 characters';
    }
    if (!/(?=.*[a-z])/.test(password)) {
      return 'Password must contain at least one lowercase letter';
    }
    if (!/(?=.*[A-Z])/.test(password)) {
      return 'Password must contain at least one uppercase letter';
    }
    if (!/(?=.*\d)/.test(password)) {
      return 'Password must contain at least one number';
    }
    return null;
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    
    // Clear error when user starts typing
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: '' }));
    }

    // Real-time password confirmation validation
    if (name === 'password_confirmation' && value && formData.password) {
      if (value !== formData.password) {
        setErrors((prev) => ({ ...prev, password_confirmation: 'Passwords do not match' }));
      } else {
        setErrors((prev) => ({ ...prev, password_confirmation: '' }));
      }
    }

    // Real-time password strength validation
    if (name === 'password' && value) {
      const passwordError = validatePassword(value);
      if (passwordError) {
        setErrors((prev) => ({ ...prev, password: passwordError }));
      } else {
        setErrors((prev) => ({ ...prev, password: '' }));
      }
      
      // Re-validate confirmation if it exists
      if (formData.password_confirmation) {
        if (value !== formData.password_confirmation) {
          setErrors((prev) => ({ ...prev, password_confirmation: 'Passwords do not match' }));
        } else {
          setErrors((prev) => ({ ...prev, password_confirmation: '' }));
        }
      }
    }
  };

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {};

    const passwordError = validatePassword(formData.password);
    if (passwordError) {
      newErrors.password = passwordError;
    }

    if (!formData.password_confirmation) {
      newErrors.password_confirmation = 'Please confirm your password';
    } else if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = 'Passwords do not match';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setAlert(null);

    if (!validate()) return;

    setIsLoading(true);
    try {
      await authService.resetPassword(formData);
      setAlert({
        type: 'success',
        message: 'Password reset successful! Redirecting to login...',
      });
      setTimeout(() => {
        navigate('/login');
      }, 2000);
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || error.response?.data?.errors
        ? Object.values(error.response.data.errors).flat().join(', ')
        : 'Failed to reset password. The link may have expired.';
      
      setAlert({
        type: 'error',
        message: errorMessage,
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 px-4 py-12 animate-fade-in">
      <div className="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 animate-slide-up">
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
          <p className="text-gray-600">Enter your new password below</p>
        </div>

        {alert && (
          <div className="mb-6">
            <Alert
              type={alert.type}
              message={alert.message}
              onClose={() => setAlert(null)}
            />
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <Input
            label="New Password"
            type="password"
            name="password"
            value={formData.password}
            onChange={handleChange}
            error={errors.password}
            required
            autoComplete="new-password"
            placeholder="Enter your new password"
            helperText="Must be at least 6 characters with uppercase, lowercase, and number"
          />

          <Input
            label="Confirm New Password"
            type="password"
            name="password_confirmation"
            value={formData.password_confirmation}
            onChange={handleChange}
            error={errors.password_confirmation}
            required
            autoComplete="new-password"
            placeholder="Confirm your new password"
          />

          <Button type="submit" fullWidth isLoading={isLoading}>
            Reset Password
          </Button>
        </form>

        <p className="mt-6 text-center text-sm text-gray-600">
          <Link
            to="/login"
            className="font-medium text-primary-600 hover:text-primary-700 transition-colors"
          >
            Back to Login
          </Link>
        </p>
      </div>
    </div>
  );
};

export default ResetPassword;

