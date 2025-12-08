import React, { useState, useEffect, useRef } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { authService } from '../services/authService';
import Input from '../components/Input';
import Button from '../components/Button';
import Alert from '../components/Alert';

const VerifyEmail: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [code, setCode] = useState(['', '', '', '', '', '']);
  const [verificationToken, setVerificationToken] = useState<string | null>(null);
  const [email, setEmail] = useState<string>('');
  const [error, setError] = useState<string>('');
  const [isLoading, setIsLoading] = useState(false);
  const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

  useEffect(() => {
    const state = location.state as { token?: string; email?: string };
    if (state?.token) {
      setVerificationToken(state.token);
      setEmail(state.email || '');
    } else {
      navigate('/login');
    }
  }, [location, navigate]);

  const handleChange = (index: number, value: string) => {
    if (!/^\d*$/.test(value)) return; // Only allow digits
    
    const newCode = [...code];
    newCode[index] = value.slice(-1); // Only take the last character
    setCode(newCode);
    setError('');

    // Auto-focus next input
    if (value && index < 5) {
      inputRefs.current[index + 1]?.focus();
    }
  };

  const handleKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Backspace' && !code[index] && index > 0) {
      inputRefs.current[index - 1]?.focus();
    }
  };

  const handlePaste = (e: React.ClipboardEvent) => {
    e.preventDefault();
    const pastedData = e.clipboardData.getData('text').slice(0, 6);
    if (/^\d+$/.test(pastedData)) {
      const newCode = pastedData.split('').concat(Array(6 - pastedData.length).fill(''));
      setCode(newCode.slice(0, 6));
      const nextIndex = Math.min(pastedData.length, 5);
      inputRefs.current[nextIndex]?.focus();
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    const codeString = code.join('');
    if (codeString.length !== 6) {
      setError('Please enter the complete 6-digit code');
      return;
    }

    if (!verificationToken) {
      setError('Verification token is missing. Please try logging in again.');
      return;
    }

    setIsLoading(true);
    try {
      const response = await authService.verifyLogin({
        verification_token: verificationToken,
        code: codeString,
      });

      if (response.token && response.user) {
        localStorage.setItem('token', response.token);
        localStorage.setItem('user', JSON.stringify(response.user));
        navigate('/dashboard');
      }
    } catch (error: any) {
      setError(
        error.response?.data?.message || 'Invalid verification code. Please try again.'
      );
      // Clear code on error
      setCode(['', '', '', '', '', '']);
      inputRefs.current[0]?.focus();
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 px-4 py-12 animate-fade-in">
      <div className="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 animate-slide-up">
        <div className="text-center mb-8">
          <div className="mx-auto w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
            <svg
              className="w-8 h-8 text-primary-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
          </div>
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Verify Your Email</h1>
          <p className="text-gray-600">
            We've sent a 6-digit verification code to
            <br />
            <span className="font-medium text-gray-900">{email}</span>
          </p>
        </div>

        {error && (
          <div className="mb-6">
            <Alert type="error" message={error} onClose={() => setError('')} />
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex gap-3 justify-center">
            {code.map((digit, index) => (
              <input
                key={index}
                ref={(el) => (inputRefs.current[index] = el)}
                type="text"
                inputMode="numeric"
                maxLength={1}
                value={digit}
                onChange={(e) => handleChange(index, e.target.value)}
                onKeyDown={(e) => handleKeyDown(index, e)}
                onPaste={handlePaste}
                className={`w-14 h-14 text-center text-2xl font-semibold rounded-lg border-2 transition-all ${
                  error
                    ? 'border-red-300 focus:border-red-500'
                    : 'border-gray-300 focus:border-primary-500'
                } focus:outline-none focus:ring-2 focus:ring-primary-200`}
                autoFocus={index === 0}
              />
            ))}
          </div>

          <Button type="submit" fullWidth isLoading={isLoading}>
            Verify Code
          </Button>
        </form>

        <p className="mt-6 text-center text-sm text-gray-600">
          Didn't receive the code?{' '}
          <button
            onClick={() => navigate('/login')}
            className="font-medium text-primary-600 hover:text-primary-700 transition-colors"
          >
            Try again
          </button>
        </p>
      </div>
    </div>
  );
};

export default VerifyEmail;

