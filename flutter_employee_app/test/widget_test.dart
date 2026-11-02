import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_employee_app/screens/login_screen.dart';

void main() {
  testWidgets('Employee Login Screen renders properly', (WidgetTester tester) async {
    await tester.pumpWidget(const MaterialApp(
      home: LoginScreen(),
    ));
    expect(find.text('PT. ASIA PLASTIK'), findsOneWidget);
    expect(find.text('Selamat Datang'), findsOneWidget);
  });
}
