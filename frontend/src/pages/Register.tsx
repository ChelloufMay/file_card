import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { authService } from '../services/authService';
import Input from '../components/Input';
import Button from '../components/Button';
import Alert from '../components/Alert';

const Register: React.FC = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [alert, setAlert] = useState<{ type: 'success' | 'error'; message: string } | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const validateEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

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

    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!validateEmail(formData.email)) {
      newErrors.email = 'Please enter a valid email address';
    }

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
      const response = await authService.register(formData);
      
      if (response.token && response.user) {
        setAlert({
          type: 'success',
          message: 'Registration successful! Redirecting to dashboard...',
        });
        localStorage.setItem('token', response.token);
        localStorage.setItem('user', JSON.stringify(response.user));
        setTimeout(() => {
          navigate('/dashboard');
        }, 1500);
      }
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || error.response?.data?.errors
        ? Object.values(error.response.data.errors).flat().join(', ')
        : 'Registration failed. Please try again.';
      
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
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Create Account</h1>
          <p className="text-gray-600">Sign up to get started</p>
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
            label="Full Name"
            type="text"
            name="name"
            value={formData.name}
            onChange={handleChange}
            error={errors.name}
            required
            autoComplete="name"
            placeholder="John Doe"
          />

          <Input
            label="Email Address"
            type="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            error={errors.email}
            required
            autoComplete="email"
            placeholder="you@example.com"
          />

          <Input
            label="Password"
            type="password"
            name="password"
            value={formData.password}
            onChange={handleChange}
            error={errors.password}
            required
            autoComplete="new-password"
            placeholder="Create a strong password"
            helperText="Must be at least 6 characters with uppercase, lowercase, and number"
          />

          <Input
            label="Confirm Password"
            type="password"
            name="password_confirmation"
            value={formData.password_confirmation}
            onChange={handleChange}
            error={errors.password_confirmation}
            required
            autoComplete="new-password"
            placeholder="Confirm your password"
          />

          <Button type="submit" fullWidth isLoading={isLoading}>
            Create Account
          </Button>
        </form>

        <p className="mt-6 text-center text-sm text-gray-600">
          Already have an account?{' '}
          <Link
            to="/login"
            className="font-medium text-primary-600 hover:text-primary-700 transition-colors"
          >
            Sign in
          </Link>
        </p>
      </div>
    </div>
  );
};

export default Register;

