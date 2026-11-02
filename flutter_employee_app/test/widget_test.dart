import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_employee_app/screens/login_screen.dart';

void main() {
  testWidgets('Employee Login Screen renders properly', (WidgetTester tester) async {
    await tester.pumpWidget(const MaterialApp(
      home: LoginScreen(),
    ));
    expect(find.text('Selamat Datang'), findsOneWidget);
    expect(find.text('Portal Masuk'), findsOneWidget);
    expect(find.text('MASUK KE SISTEM'), findsOneWidget);
  });
}
