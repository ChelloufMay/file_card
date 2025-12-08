import React, { InputHTMLAttributes, forwardRef } from 'react';

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  helperText?: string;
}

const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ label, error, helperText, className = '', ...props }, ref) => {
    const baseClasses = 'w-full px-4 py-3 rounded-lg border transition-all duration-200 focus:outline-none focus:ring-2';
    const errorClasses = error
      ? 'border-red-300 focus:border-red-500 focus:ring-red-200 bg-red-50'
      : 'border-gray-300 focus:border-primary-500 focus:ring-primary-200 bg-white';
    const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed' : '';

    return (
      <div className="w-full animate-fade-in">
        {label && (
          <label className="block text-sm font-medium text-gray-700 mb-2">
            {label}
            {props.required && <span className="text-red-500 ml-1">*</span>}
          </label>
        )}
        <input
          ref={ref}
          className={`${baseClasses} ${errorClasses} ${disabledClasses} ${className}`}
          {...props}
        />
        {error && (
          <p className="mt-1 text-sm text-red-600 animate-slide-down">{error}</p>
        )}
        {helperText && !error && (
          <p className="mt-1 text-sm text-gray-500">{helperText}</p>
        )}
      </div>
    );
  }
);

Input.displayName = 'Input';

export default Input;

