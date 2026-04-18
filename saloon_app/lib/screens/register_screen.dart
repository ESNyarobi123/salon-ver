import 'dart:math' as math;
import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';

import '../core/theme.dart';
import '../salon_strings.dart';
import '../providers/auth_provider.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen>
    with TickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  final _firstNameController = TextEditingController();
  final _lastNameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _locationController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  int _currentStep = 0;

  late AnimationController _shakeController;
  late Animation<double> _shakeAnimation;

  late AnimationController _fadeController;
  late Animation<double> _logoScale;
  late Animation<double> _contentFade;
  late Animation<Offset> _formSlide;

  @override
  void initState() {
    super.initState();
    _shakeController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );
    _shakeAnimation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _shakeController, curve: Curves.easeInOut),
    );

    _fadeController = AnimationController(
      duration: const Duration(milliseconds: 1200),
      vsync: this,
    );
    _logoScale = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _fadeController,
        curve: const Interval(0.0, 0.5, curve: Curves.elasticOut),
      ),
    );
    _contentFade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _fadeController,
        curve: const Interval(0.3, 0.7, curve: Curves.easeOut),
      ),
    );
    _formSlide = Tween<Offset>(begin: const Offset(0, 0.15), end: Offset.zero)
        .animate(
          CurvedAnimation(
            parent: _fadeController,
            curve: const Interval(0.4, 1.0, curve: Curves.easeOutCubic),
          ),
        );

    _fadeController.forward();
  }

  @override
  void dispose() {
    _firstNameController.dispose();
    _lastNameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _locationController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    _shakeController.dispose();
    _fadeController.dispose();
    super.dispose();
  }

  void _nextStep() {
    if (_currentStep == 0) {
      if (_formKey.currentState!.validate()) {
        setState(() {
          _currentStep = 1;
        });
        _fadeController.reset();
        _fadeController.forward();
      }
    }
  }

  void _previousStep() {
    if (_currentStep == 1) {
      setState(() {
        _currentStep = 0;
      });
      _fadeController.reset();
      _fadeController.forward();
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.register(
      firstName: _firstNameController.text.trim(),
      lastName: _lastNameController.text.trim(),
      email: _emailController.text.trim(),
      phone: _phoneController.text.trim(),
      location: _locationController.text.trim().isEmpty
          ? null
          : _locationController.text.trim(),
      password: _passwordController.text,
      passwordConfirmation: _confirmPasswordController.text,
    );
    if (ok && mounted) {
      _showSuccessDialog();
    } else if (!ok && mounted) {
      _shakeController.forward(from: 0);
      await Future.delayed(const Duration(milliseconds: 100));
      if (mounted) _shakeController.reverse();
    }
  }

  void _showSuccessDialog() {
    final auth = context.read<AuthProvider>();
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        child: Container(
          padding: const EdgeInsets.all(28),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                const Color(0xFF1A0F2E).withValues(alpha: 0.95),
                const Color(0xFF0D1B2A).withValues(alpha: 0.95),
              ],
            ),
            border: Border.all(
              color: Colors.white.withValues(alpha: 0.1),
              width: 1.5,
            ),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: AppTheme.primaryGradient,
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.primary.withValues(alpha: 0.3),
                      blurRadius: 24,
                      spreadRadius: 4,
                    ),
                  ],
                ),
                child: const Icon(
                  Icons.check_rounded,
                  color: Colors.white,
                  size: 48,
                ),
              ),
              const SizedBox(height: 24),
              Text(
                SalonStrings.registerSuccessTitle,
                style: GoogleFonts.poppins(
                  fontSize: 24,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                auth.registrationMessage ??
                    SalonStrings.registerSuccessBodyDefault,
                textAlign: TextAlign.center,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: Colors.white.withValues(alpha: 0.7),
                  height: 1.5,
                ),
              ),
              if (auth.user?.globalWaiterNumber != null) ...[
                const SizedBox(height: 20),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 12,
                  ),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    gradient: LinearGradient(
                      colors: [
                        AppTheme.primary.withValues(alpha: 0.2),
                        AppTheme.secondary.withValues(alpha: 0.2),
                      ],
                    ),
                    border: Border.all(
                      color: AppTheme.primary.withValues(alpha: 0.3),
                    ),
                  ),
                  child: Column(
                    children: [
                      Text(
                        SalonStrings.registerUniqueNumber,
                        style: GoogleFonts.poppins(
                          fontSize: 11,
                          color: Colors.white.withValues(alpha: 0.5),
                          letterSpacing: 0.5,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        auth.user!.globalWaiterNumber!,
                        style: GoogleFonts.poppins(
                          fontSize: 18,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.primary,
                          letterSpacing: 1,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
              const SizedBox(height: 28),
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).pop();
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 0,
                  ),
                  child: Text(
                    SalonStrings.registerGotIt,
                    style: GoogleFonts.poppins(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final screenHeight = MediaQuery.of(context).size.height;

    return Scaffold(
      resizeToAvoidBottomInset: true,
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Color(0xFF0F0A1E), Color(0xFF1A0F2E), Color(0xFF0D1B2A)],
          ),
        ),
        child: Stack(
          children: [
            _buildBackgroundArt(screenHeight),
            SafeArea(
              child: Column(
                children: [
                  Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        IconButton(
                          onPressed: () => Navigator.pop(context),
                          icon: const Icon(
                            Icons.arrow_back_rounded,
                            color: Colors.white,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Expanded(
                    child: Center(
                      child: SingleChildScrollView(
                        physics: const BouncingScrollPhysics(),
                        padding: const EdgeInsets.symmetric(
                          horizontal: 24,
                          vertical: 20,
                        ),
                        child: AnimatedBuilder(
                          animation: _shakeAnimation,
                          builder: (context, child) {
                            final shake =
                                math.sin(_shakeAnimation.value * math.pi * 6) *
                                10;
                            return Transform.translate(
                              offset: Offset(shake, 0),
                              child: child,
                            );
                          },
                          child: Form(
                            key: _formKey,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                ScaleTransition(
                                  scale: _logoScale,
                                  child: _buildLogo(),
                                ),
                                const SizedBox(height: 24),
                                FadeTransition(
                                  opacity: _contentFade,
                                  child: Column(
                                    children: [
                                      _buildTitle(),
                                      const SizedBox(height: 6),
                                      Text(
                                        SalonStrings.registerHeadline,
                                        style: GoogleFonts.poppins(
                                          color: Colors.white.withValues(
                                            alpha: 0.4,
                                          ),
                                          fontSize: 13,
                                          letterSpacing: 0.5,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      _buildStepIndicator(),
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 32),
                                SlideTransition(
                                  position: _formSlide,
                                  child: FadeTransition(
                                    opacity: _contentFade,
                                    child: _currentStep == 0
                                        ? _buildStep1Form()
                                        : _buildStep2Form(),
                                  ),
                                ),
                                _buildErrorDisplay(),
                                const SizedBox(height: 24),
                                SlideTransition(
                                  position: _formSlide,
                                  child: FadeTransition(
                                    opacity: _contentFade,
                                    child: _currentStep == 0
                                        ? _buildNextButton()
                                        : _buildRegisterButton(),
                                  ),
                                ),
                                const SizedBox(height: 20),
                                FadeTransition(
                                  opacity: _contentFade,
                                  child: _buildLoginLink(),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBackgroundArt(double screenHeight) {
    return Stack(
      children: [
        Positioned(
          top: -80,
          right: -60,
          child: Container(
            width: 280,
            height: 280,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: AppTheme.primary.withValues(alpha: 0.12),
                  blurRadius: 120,
                  spreadRadius: 30,
                ),
              ],
            ),
          ),
        ),
        Positioned(
          bottom: screenHeight * 0.15,
          left: -80,
          child: Container(
            width: 220,
            height: 220,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: AppTheme.secondary.withValues(alpha: 0.1),
                  blurRadius: 100,
                  spreadRadius: 20,
                ),
              ],
            ),
          ),
        ),
        Positioned(
          top: screenHeight * 0.3,
          left: 0,
          right: 0,
          child: Center(
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF6366F1).withValues(alpha: 0.06),
                    blurRadius: 150,
                    spreadRadius: 40,
                  ),
                ],
              ),
            ),
          ),
        ),
        Positioned.fill(child: CustomPaint(painter: _GridPatternPainter())),
      ],
    );
  }

  Widget _buildLogo() {
    return Container(
      width: 80,
      height: 80,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primary.withValues(alpha: 0.3),
            blurRadius: 32,
            offset: const Offset(0, 12),
          ),
          BoxShadow(
            color: AppTheme.secondary.withValues(alpha: 0.2),
            blurRadius: 48,
            offset: const Offset(0, 16),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
        child: Image.asset(
          'assets/icon.png',
          width: 80,
          height: 80,
          fit: BoxFit.cover,
        ),
      ),
    );
  }

  Widget _buildTitle() {
    return ShaderMask(
      blendMode: BlendMode.srcIn,
      shaderCallback: (bounds) => const LinearGradient(
        colors: [Color(0xFF06B6D4), Color(0xFF8B5CF6), Color(0xFFEC4899)],
      ).createShader(bounds),
      child: Text(
        SalonStrings.registerJoin,
        style: GoogleFonts.poppins(
          fontSize: 32,
          fontWeight: FontWeight.w900,
          color: Colors.white,
          letterSpacing: 3,
          height: 1.1,
        ),
      ),
    );
  }

  Widget _buildStepIndicator() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        _buildStepDot(0, SalonStrings.registerStepPersonal),
        Container(
          width: 40,
          height: 2,
          margin: const EdgeInsets.symmetric(horizontal: 8),
          decoration: BoxDecoration(
            gradient: _currentStep >= 1 ? AppTheme.primaryGradient : null,
            color: _currentStep >= 1
                ? null
                : Colors.white.withValues(alpha: 0.2),
          ),
        ),
        _buildStepDot(1, SalonStrings.registerStepSecurity),
      ],
    );
  }

  Widget _buildStepDot(int step, String label) {
    final isActive = _currentStep == step;
    final isCompleted = _currentStep > step;
    return Column(
      children: [
        Container(
          width: 32,
          height: 32,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            gradient: isActive || isCompleted ? AppTheme.primaryGradient : null,
            color: isActive || isCompleted
                ? null
                : Colors.white.withValues(alpha: 0.1),
            border: Border.all(
              color: isActive
                  ? AppTheme.primary
                  : Colors.white.withValues(alpha: 0.2),
              width: 2,
            ),
          ),
          child: Center(
            child: isCompleted
                ? const Icon(Icons.check_rounded, color: Colors.white, size: 16)
                : Text(
                    '${step + 1}',
                    style: GoogleFonts.poppins(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
          ),
        ),
        const SizedBox(height: 6),
        Text(
          label,
          style: GoogleFonts.poppins(
            fontSize: 10,
            color: isActive
                ? AppTheme.primary
                : Colors.white.withValues(alpha: 0.4),
            fontWeight: isActive ? FontWeight.w600 : FontWeight.w400,
          ),
        ),
      ],
    );
  }

  Widget _buildStep1Form() {
    return ClipRRect(
      borderRadius: BorderRadius.circular(24),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color: Colors.white.withValues(alpha: 0.1),
              width: 1.5,
            ),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                Colors.white.withValues(alpha: 0.08),
                Colors.white.withValues(alpha: 0.03),
              ],
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                SalonStrings.registerPersonalTitle,
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                ),
              ),
              Text(
                SalonStrings.registerPersonalSubtitle,
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  color: Colors.white.withValues(alpha: 0.4),
                ),
              ),
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: _buildInputField(
                      controller: _firstNameController,
                      label: SalonStrings.labelFirstName,
                      hint: SalonStrings.hintFirstName,
                      icon: Icons.person_outline_rounded,
                      validator: (v) {
                        if (v == null || v.trim().isEmpty) {
                          return SalonStrings.valEnterFirstName;
                        }
                        return null;
                      },
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildInputField(
                      controller: _lastNameController,
                      label: SalonStrings.labelLastName,
                      hint: SalonStrings.hintLastName,
                      icon: Icons.person_outline_rounded,
                      validator: (v) {
                        if (v == null || v.trim().isEmpty) {
                          return SalonStrings.valEnterLastName;
                        }
                        return null;
                      },
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              _buildInputField(
                controller: _emailController,
                label: SalonStrings.labelEmailRegister,
                hint: SalonStrings.hintEmailRegister,
                icon: Icons.alternate_email_rounded,
                keyboardType: TextInputType.emailAddress,
                validator: (v) {
                  if (v == null || v.trim().isEmpty) {
                    return SalonStrings.valEnterEmail;
                  }
                  if (!v.contains('@')) return SalonStrings.valInvalidEmail;
                  return null;
                },
              ),
              const SizedBox(height: 16),
              _buildInputField(
                controller: _phoneController,
                label: SalonStrings.labelPhone,
                hint: '255789123456',
                icon: Icons.phone_outlined,
                keyboardType: TextInputType.phone,
                validator: (v) {
                  if (v == null || v.trim().isEmpty) {
                    return SalonStrings.valEnterPhone;
                  }
                  if (v.trim().length < 10) {
                    return SalonStrings.valInvalidPhone;
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              _buildInputField(
                controller: _locationController,
                label: SalonStrings.labelLocationOptional,
                hint: SalonStrings.hintLocation,
                icon: Icons.location_on_outlined,
                validator: null,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStep2Form() {
    return ClipRRect(
      borderRadius: BorderRadius.circular(24),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color: Colors.white.withValues(alpha: 0.1),
              width: 1.5,
            ),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                Colors.white.withValues(alpha: 0.08),
                Colors.white.withValues(alpha: 0.03),
              ],
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                SalonStrings.registerSecurityTitle,
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                ),
              ),
              Text(
                SalonStrings.registerSecuritySubtitle,
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  color: Colors.white.withValues(alpha: 0.4),
                ),
              ),
              const SizedBox(height: 20),
              _buildInputField(
                controller: _passwordController,
                label: SalonStrings.labelPassword,
                hint: '••••••••',
                icon: Icons.lock_outline_rounded,
                obscure: _obscurePassword,
                suffixIcon: IconButton(
                  icon: Icon(
                    _obscurePassword
                        ? Icons.visibility_off_rounded
                        : Icons.visibility_rounded,
                    color: Colors.white.withValues(alpha: 0.4),
                    size: 20,
                  ),
                  onPressed: () =>
                      setState(() => _obscurePassword = !_obscurePassword),
                ),
                validator: (v) {
                  if (v == null || v.isEmpty) return SalonStrings.valEnterPassword;
                  if (v.length < 8) {
                    return SalonStrings.valPasswordMin8;
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              _buildInputField(
                controller: _confirmPasswordController,
                label: SalonStrings.labelConfirmPassword,
                hint: '••••••••',
                icon: Icons.lock_outline_rounded,
                obscure: _obscureConfirmPassword,
                suffixIcon: IconButton(
                  icon: Icon(
                    _obscureConfirmPassword
                        ? Icons.visibility_off_rounded
                        : Icons.visibility_rounded,
                    color: Colors.white.withValues(alpha: 0.4),
                    size: 20,
                  ),
                  onPressed: () => setState(
                    () => _obscureConfirmPassword = !_obscureConfirmPassword,
                  ),
                ),
                validator: (v) {
                  if (v == null || v.isEmpty) {
                    return SalonStrings.valConfirmPassword;
                  }
                  if (v != _passwordController.text) {
                    return SalonStrings.valPasswordsNoMatch;
                  }
                  return null;
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInputField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    TextInputType? keyboardType,
    bool obscure = false,
    Widget? suffixIcon,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.poppins(
            fontSize: 11,
            fontWeight: FontWeight.w600,
            color: Colors.white.withValues(alpha: 0.6),
            letterSpacing: 0.5,
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          obscureText: obscure,
          style: GoogleFonts.poppins(color: Colors.white, fontSize: 14),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: GoogleFonts.poppins(
              color: Colors.white.withValues(alpha: 0.2),
              fontSize: 13,
            ),
            prefixIcon: Icon(
              icon,
              color: Colors.white.withValues(alpha: 0.4),
              size: 18,
            ),
            suffixIcon: suffixIcon,
            filled: true,
            fillColor: Colors.white.withValues(alpha: 0.06),
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 14,
              vertical: 14,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide.none,
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(
                color: Colors.white.withValues(alpha: 0.08),
              ),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppTheme.primary, width: 1.5),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppTheme.error),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppTheme.error, width: 1.5),
            ),
            errorStyle: GoogleFonts.poppins(
              fontSize: 10,
              color: AppTheme.error,
            ),
          ),
          validator: validator,
        ),
      ],
    );
  }

  Widget _buildErrorDisplay() {
    final error = context.watch<AuthProvider>().error;
    if (error == null) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.only(top: 16),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: AppTheme.error.withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppTheme.error.withValues(alpha: 0.3)),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppTheme.error.withValues(alpha: 0.2),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.error_outline_rounded,
                color: AppTheme.error,
                size: 16,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                error,
                style: GoogleFonts.poppins(
                  color: AppTheme.error,
                  fontSize: 13,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRegisterButton() {
    final isLoading = context.watch<AuthProvider>().isLoading;

    return Column(
      children: [
        Container(
          width: double.infinity,
          height: 56,
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              begin: Alignment.centerLeft,
              end: Alignment.centerRight,
              colors: [Color(0xFF06B6D4), Color(0xFF8B5CF6)],
            ),
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF8B5CF6).withValues(alpha: 0.35),
                blurRadius: 24,
                offset: const Offset(0, 8),
              ),
              BoxShadow(
                color: const Color(0xFF06B6D4).withValues(alpha: 0.2),
                blurRadius: 16,
                offset: const Offset(-4, 4),
              ),
            ],
          ),
          child: ElevatedButton(
            onPressed: isLoading ? null : _submit,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.transparent,
              shadowColor: Colors.transparent,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              padding: EdgeInsets.zero,
            ),
            child: isLoading
                ? const SizedBox(
                    width: 24,
                    height: 24,
                    child: CircularProgressIndicator(
                      strokeWidth: 2.5,
                      color: Colors.white,
                    ),
                  )
                : Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        SalonStrings.registerCreateAccount,
                        style: GoogleFonts.poppins(
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 1.5,
                        ),
                      ),
                      const SizedBox(width: 10),
                      const Icon(Icons.arrow_forward_rounded, size: 20),
                    ],
                  ),
          ),
        ),
        const SizedBox(height: 16),
        TextButton.icon(
          onPressed: _previousStep,
          icon: const Icon(Icons.arrow_back_rounded, size: 18),
          label: Text(
            SalonStrings.registerBack,
            style: GoogleFonts.poppins(
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
          ),
          style: TextButton.styleFrom(
            foregroundColor: Colors.white.withValues(alpha: 0.7),
          ),
        ),
      ],
    );
  }

  Widget _buildNextButton() {
    return Column(
      children: [
        Container(
          width: double.infinity,
          height: 56,
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              begin: Alignment.centerLeft,
              end: Alignment.centerRight,
              colors: [Color(0xFF06B6D4), Color(0xFF8B5CF6)],
            ),
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF8B5CF6).withValues(alpha: 0.35),
                blurRadius: 24,
                offset: const Offset(0, 8),
              ),
              BoxShadow(
                color: const Color(0xFF06B6D4).withValues(alpha: 0.2),
                blurRadius: 16,
                offset: const Offset(-4, 4),
              ),
            ],
          ),
          child: ElevatedButton(
            onPressed: _nextStep,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.transparent,
              shadowColor: Colors.transparent,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              padding: EdgeInsets.zero,
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  SalonStrings.registerNext,
                  style: GoogleFonts.poppins(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    letterSpacing: 1.5,
                  ),
                ),
                const SizedBox(width: 10),
                const Icon(Icons.arrow_forward_rounded, size: 20),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildLoginLink() {
    return GestureDetector(
      onTap: () => Navigator.pop(context),
      child: RichText(
        text: TextSpan(
          text: SalonStrings.registerAlreadyHave,
          style: GoogleFonts.poppins(
            fontSize: 13,
            color: Colors.white.withValues(alpha: 0.5),
          ),
          children: [
            TextSpan(
              text: SalonStrings.registerSignInLink,
              style: GoogleFonts.poppins(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: AppTheme.primary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _GridPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.02)
      ..strokeWidth = 0.5;

    const spacing = 40.0;

    for (double x = 0; x < size.width; x += spacing) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), paint);
    }
    for (double y = 0; y < size.height; y += spacing) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
