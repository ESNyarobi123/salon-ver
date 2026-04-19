import 'package:flutter/material.dart';

/// Compact "age" since booking was created (e.g. 7s, 15m, 10h, 12d).
String formatBookingAgeShort(DateTime createdAt) {
  final now = DateTime.now();
  final created = createdAt.toLocal();
  final seconds = now.difference(created).inSeconds;
  final s = seconds < 0 ? 0 : seconds;
  if (s < 1) return 'now';
  if (s < 60) return '${s}s';
  if (s < 3600) return '${s ~/ 60}m';
  if (s < 86400) return '${s ~/ 3600}h';
  return '${s ~/ 86400}d';
}

/// `HH:mm` in local time for API `scheduled_time`.
String formatApiTime(TimeOfDay t) {
  final h = t.hour.toString().padLeft(2, '0');
  final m = t.minute.toString().padLeft(2, '0');
  return '$h:$m';
}
