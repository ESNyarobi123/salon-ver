import 'package:flutter_test/flutter_test.dart';

import 'package:tiptap/main.dart';

void main() {
  testWidgets('App smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const TiptapApp());
    await tester.pump();
    // SplashScreen runs a repeating animation; avoid pumpAndSettle (times out).
    await tester.pump(const Duration(milliseconds: 100));
    expect(find.byType(TiptapApp), findsOneWidget);
  });
}

