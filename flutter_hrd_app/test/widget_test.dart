import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_hrd_app/screens/login_hrd_screen.dart';

void main() {
  testWidgets('HRD Login Screen renders properly', (WidgetTester tester) async {
    await tester.pumpWidget(const MaterialApp(
      home: LoginHrdScreen(),
    ));
    expect(find.text('PT. ASIA PLASTIK'), findsOneWidget);
    expect(find.text('Selamat Datang, Tim HRD'), findsOneWidget);
  });
}
