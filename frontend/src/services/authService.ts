import api from './api';

export interface LoginData {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone?: string;
  phone_carrier?: string;
}

export interface VerifyLoginData {
  verification_token: string;
  code: string;
}

export interface ForgotPasswordData {
  email: string;
}

export interface ResetPasswordData {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export const authService = {
  login: async (data: LoginData) => {
    const response = await api.post('/login', data);
    return response.data;
  },

  register: async (data: RegisterData) => {
    const response = await api.post('/register', data);
    return response.data;
  },

  verifyLogin: async (data: VerifyLoginData) => {
    const response = await api.post('/verify-login', data);
    return response.data;
  },

  forgotPassword: async (data: ForgotPasswordData) => {
    const response = await api.post('/forgot-password', data);
    return response.data;
  },

  resetPassword: async (data: ResetPasswordData) => {
    const response = await api.post('/reset-password', data);
    return response.data;
  },

  logout: async () => {
    await api.post('/logout');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  },

  getCurrentUser: async () => {
    const response = await api.get('/user');
    return response.data;
  },
};

