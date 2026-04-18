import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:tiptap_live_order/main.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  setUp(() {
    SharedPreferences.setMockInitialValues({});
  });

  testWidgets('App smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const TiptapOrderPortal());
    await tester.pump();
    // Splash: Future.delayed(1800ms) before auth gate
    await tester.pump(const Duration(milliseconds: 2000));
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 600));
    expect(find.byType(TiptapOrderPortal), findsOneWidget);
  });
}
