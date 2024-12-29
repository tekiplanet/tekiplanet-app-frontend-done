import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useAuthStore } from "@/store/useAuthStore";

const EmailVerification = () => {
  const [code, setCode] = useState('');
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();
  const authStore = useAuthStore();
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const requiresVerification = useAuthStore((state) => state.requiresVerification);
  const user = useAuthStore((state) => state.user);

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
      return;
    }

    if (!requiresVerification) {
      navigate('/dashboard');
    }
  }, [isAuthenticated, requiresVerification, navigate]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    try {
      await authStore.verifyEmail(code);
      await authStore.initialize();
      toast.success('Email verified successfully');
      navigate('/dashboard');
    } catch (error: any) {
      toast.error(error.message || 'Failed to verify email');
      setLoading(false);
    }
  };

  const handleResend = async () => {
    try {
      await authStore.resendVerification();
      toast.success('Verification email sent');
      // Clear the input field when resending
      setCode('');
    } catch (error: any) {
      toast.error(error.message || 'Failed to resend verification email');
    }
  };

  // Only show if authenticated (even without full user data)
  if (!isAuthenticated) {
    return null;
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
      <div className="w-full max-w-md">
        <div className="bg-white shadow-lg rounded-lg p-8">
          <h1 className="text-2xl font-bold text-center mb-2">Verify Your Email</h1>
          <p className="text-gray-600 text-center mb-6">
            Please enter the verification code sent to {user.email}
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <Input
                type="text"
                placeholder="Enter 6-digit code"
                value={code}
                onChange={(e) => setCode(e.target.value.replace(/\D/g, '').slice(0, 6))}
                maxLength={6}
                className="text-center text-2xl tracking-[0.5em] font-mono"
              />
            </div>

            <Button 
              type="submit" 
              className="w-full"
              disabled={loading || code.length !== 6}
            >
              {loading ? 'Verifying...' : 'Verify Email'}
            </Button>

            <div className="text-center">
              <p className="text-sm text-gray-600 mb-2">
                Didn't receive the code?
              </p>
              <Button
                type="button"
                variant="ghost"
                onClick={handleResend}
                disabled={loading}
                className="text-primary hover:text-primary/90"
              >
                Resend Code
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default EmailVerification; 