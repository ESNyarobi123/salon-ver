import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Brand Colors
  static const Color primary = Color(0xFFFF6B35);
  static const Color primaryDark = Color(0xFFE85A24);
  static const Color primaryLight = Color(0xFFFF8C5E);
  static const Color secondary = Color(0xFF2D3250);
  static const Color accent = Color(0xFF7C83FD);
  static const Color success = Color(0xFF00C896);
  static const Color warning = Color(0xFFFFB830);
  static const Color error = Color(0xFFFF4D6D);
  static const Color info = Color(0xFF4FC3F7);

  // Neutral
  static const Color bg = Color(0xFF0F1117);
  static const Color surface = Color(0xFF1A1D2E);
  static const Color surfaceVariant = Color(0xFF252840);
  static const Color border = Color(0xFF2E3150);
  static const Color textPrimary = Color(0xFFF5F6FA);
  static const Color textSecondary = Color(0xFF9B9FC4);
  static const Color textMuted = Color(0xFF5C607E);

  // Status colors
  static const Color statusPending = Color(0xFFFFB830);
  static const Color statusPreparing = Color(0xFF4FC3F7);
  static const Color statusServed = Color(0xFF7C83FD);
  static const Color statusPaid = Color(0xFF00C896);

  static ThemeData get dark {
    return ThemeData(
      brightness: Brightness.dark,
      scaffoldBackgroundColor: bg,
      primaryColor: primary,
      colorScheme: const ColorScheme.dark(
        primary: primary,
        secondary: accent,
        surface: surface,
        error: error,
        onPrimary: Colors.white,
        onSecondary: Colors.white,
        onSurface: textPrimary,
        onError: Colors.white,
      ),
      textTheme: GoogleFonts.poppinsTextTheme(
        const TextTheme(
          displayLarge:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w700),
          displayMedium:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w700),
          displaySmall:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w600),
          headlineLarge:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w700),
          headlineMedium:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w600),
          headlineSmall:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w600),
          titleLarge:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w600),
          titleMedium:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w500),
          titleSmall:
              TextStyle(color: textSecondary, fontWeight: FontWeight.w500),
          bodyLarge: TextStyle(color: textPrimary),
          bodyMedium: TextStyle(color: textSecondary),
          bodySmall: TextStyle(color: textMuted),
          labelLarge:
              TextStyle(color: textPrimary, fontWeight: FontWeight.w600),
          labelMedium: TextStyle(color: textSecondary),
          labelSmall: TextStyle(color: textMuted),
        ),
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: surface,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: GoogleFonts.poppins(
          color: textPrimary,
          fontSize: 18,
          fontWeight: FontWeight.w600,
        ),
        iconTheme: const IconThemeData(color: textPrimary),
      ),
      cardTheme: CardThemeData(
        color: surface,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: border, width: 1),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surfaceVariant,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: error),
        ),
        labelStyle: const TextStyle(color: textSecondary),
        hintStyle: const TextStyle(color: textMuted),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primary,
          foregroundColor: Colors.white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: GoogleFonts.poppins(
            fontWeight: FontWeight.w600,
            fontSize: 15,
          ),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: primary,
          side: const BorderSide(color: primary),
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: GoogleFonts.poppins(
            fontWeight: FontWeight.w600,
            fontSize: 15,
          ),
        ),
      ),
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: primary,
          textStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600),
        ),
      ),
      bottomNavigationBarTheme: const BottomNavigationBarThemeData(
        backgroundColor: surface,
        selectedItemColor: primary,
        unselectedItemColor: textMuted,
      ),
      chipTheme: ChipThemeData(
        backgroundColor: surfaceVariant,
        selectedColor: primary.withOpacity(0.2),
        labelStyle: const TextStyle(color: textSecondary),
        side: const BorderSide(color: border),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      ),
      dividerTheme: const DividerThemeData(
        color: border,
        thickness: 1,
      ),
      snackBarTheme: SnackBarThemeData(
        backgroundColor: surfaceVariant,
        contentTextStyle: GoogleFonts.poppins(color: textPrimary),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  static Color getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return statusPending;
      case 'preparing':
        return statusPreparing;
      case 'served':
        return statusServed;
      case 'paid':
        return statusPaid;
      default:
        return textMuted;
    }
  }

  static IconData getStatusIcon(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return Icons.hourglass_empty_rounded;
      case 'preparing':
        return Icons.restaurant_rounded;
      case 'served':
        return Icons.check_circle_outline_rounded;
      case 'paid':
        return Icons.payments_rounded;
      default:
        return Icons.circle_outlined;
    }
  }
}
