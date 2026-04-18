import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import '../salon_strings.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'orders_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscure = true;
  String? _error;
  final ApiService _api = ApiService();

  @override
  void dispose() {
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final result = await _api.login(_passwordController.text.trim());
      if (!mounted) return;

      if (result['success'] == true) {
        HapticFeedback.lightImpact();
        Navigator.of(context).pushReplacement(
          PageRouteBuilder(
            pageBuilder: (_, __, ___) => const OrdersScreen(),
            transitionsBuilder: (_, anim, __, child) =>
                FadeTransition(opacity: anim, child: child),
            transitionDuration: const Duration(milliseconds: 500),
          ),
        );
      } else {
        setState(() {
          _error = result['message'] ?? SalonStrings.loginFailed;
          _isLoading = false;
        });
        HapticFeedback.mediumImpact();
      }
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = SalonStrings.loginConnectionError;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isTablet = size.width > 600;

    return Scaffold(
      backgroundColor: AppTheme.bg,
      body: Stack(
        children: [
          // Background gradient blobs
          Positioned(
            top: -100,
            right: -80,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: RadialGradient(
                  colors: [
                    AppTheme.primary.withOpacity(0.3),
                    Colors.transparent,
                  ],
                ),
              ),
            ),
          ),
          Positioned(
            bottom: -60,
            left: -60,
            child: Container(
              width: 250,
              height: 250,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: RadialGradient(
                  colors: [
                    AppTheme.accent.withOpacity(0.2),
                    Colors.transparent,
                  ],
                ),
              ),
            ),
          ),

          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: EdgeInsets.symmetric(
                  horizontal: isTablet ? size.width * 0.25 : 24,
                  vertical: 32,
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Logo + Brand
                      Center(
                        child: Column(
                          children: [
                            Container(
                              width: isTablet ? 140 : 120,
                              height: isTablet ? 140 : 120,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color: Colors.white.withValues(alpha: 0.3),
                                  width: 2,
                                ),
                                boxShadow: [
                                  BoxShadow(
                                    color:
                                        AppTheme.primary.withValues(alpha: 0.3),
                                    blurRadius: 30,
                                    spreadRadius: 5,
                                  ),
                                ],
                              ),
                              child: ClipOval(
                                child: Image.asset(
                                  'assets/images/logo.jpeg',
                                  fit: BoxFit.cover,
                                ),
                              ),
                            ).animate().scale(
                                  delay: 200.ms,
                                  duration: 600.ms,
                                  curve: Curves.elasticOut,
                                ),
                            const SizedBox(height: 20),
                            Text(
                              SalonStrings.brandMark,
                              style: GoogleFonts.poppins(
                                fontSize: isTablet ? 40 : 32,
                                fontWeight: FontWeight.w800,
                                color: AppTheme.textPrimary,
                                letterSpacing: 4,
                              ),
                            )
                                .animate()
                                .fadeIn(delay: 400.ms, duration: 600.ms)
                                .slideY(begin: 0.3),
                            Text(
                              SalonStrings.brandSubtitle,
                              style: GoogleFonts.poppins(
                                fontSize: isTablet ? 18 : 15,
                                color: AppTheme.primary,
                                fontWeight: FontWeight.w500,
                                letterSpacing: 2,
                              ),
                            ).animate().fadeIn(delay: 500.ms, duration: 600.ms),
                          ],
                        ),
                      ),
                      const SizedBox(height: 48),

                      // Card
                      Container(
                        padding: const EdgeInsets.all(28),
                        decoration: BoxDecoration(
                          color: AppTheme.surface,
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(color: AppTheme.border),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.3),
                              blurRadius: 30,
                              offset: const Offset(0, 10),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              SalonStrings.loginWelcomeTitle,
                              style: GoogleFonts.poppins(
                                fontSize: isTablet ? 24 : 20,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              SalonStrings.loginWelcomeSubtitle,
                              style: GoogleFonts.poppins(
                                fontSize: 13,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 28),

                            // Password field
                            TextFormField(
                              controller: _passwordController,
                              obscureText: _obscure,
                              style:
                                  const TextStyle(color: AppTheme.textPrimary),
                              decoration: InputDecoration(
                                labelText: SalonStrings.labelPasswordField,
                                hintText: SalonStrings.passwordFieldHint,
                                prefixIcon: const Icon(
                                    Icons.lock_outline_rounded,
                                    color: AppTheme.textSecondary),
                                suffixIcon: IconButton(
                                  icon: Icon(
                                    _obscure
                                        ? Icons.visibility_off_outlined
                                        : Icons.visibility_outlined,
                                    color: AppTheme.textSecondary,
                                  ),
                                  onPressed: () =>
                                      setState(() => _obscure = !_obscure),
                                ),
                              ),
                              validator: (v) {
                                if (v == null || v.isEmpty) {
                                  return SalonStrings.passwordRequiredValidation;
                                }
                                return null;
                              },
                              onFieldSubmitted: (_) => _login(),
                            ),

                            // Error
                            if (_error != null) ...[
                              const SizedBox(height: 16),
                              Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: AppTheme.error.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(
                                      color: AppTheme.error.withOpacity(0.3)),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.error_outline_rounded,
                                        color: AppTheme.error, size: 18),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        _error!,
                                        style: GoogleFonts.poppins(
                                          fontSize: 12,
                                          color: AppTheme.error,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ).animate().shake(),
                            ],

                            const SizedBox(height: 24),
                            SizedBox(
                              width: double.infinity,
                              height: 52,
                              child: ElevatedButton(
                                onPressed: _isLoading ? null : _login,
                                child: _isLoading
                                    ? const SizedBox(
                                        width: 22,
                                        height: 22,
                                        child: CircularProgressIndicator(
                                          color: Colors.white,
                                          strokeWidth: 2.5,
                                        ),
                                      )
                                    : const Text(SalonStrings.loginButton),
                              ),
                            ),
                          ],
                        ),
                      )
                          .animate()
                          .fadeIn(delay: 600.ms, duration: 600.ms)
                          .slideY(begin: 0.2),

                      const SizedBox(height: 32),
                      Center(
                        child: Text(
                          SalonStrings.poweredByFooter,
                          style: GoogleFonts.poppins(
                            fontSize: 12,
                            color: AppTheme.textMuted,
                          ),
                        ),
                      ).animate().fadeIn(delay: 900.ms),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
