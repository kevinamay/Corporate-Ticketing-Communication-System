class UserModel {
  final int id;
  final String name;
  final String email;
  final String? whatsappNumber;
  final String? nationalIdKtp;
  final String role;
  final String? avatar;
  final int? departmentId;
  final String? departmentName;
  final bool isHrdOrAdmin;
  final bool isApproved;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.whatsappNumber,
    this.nationalIdKtp,
    required this.role,
    this.avatar,
    this.departmentId,
    this.departmentName,
    required this.isHrdOrAdmin,
    required this.isApproved,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    String? deptName;
    if (json['department'] != null && json['department'] is Map) {
      deptName = json['department']['name'];
    } else if (json['department_name'] != null) {
      deptName = json['department_name'];
    }

    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      whatsappNumber: json['whatsapp_number'],
      nationalIdKtp: json['national_id_ktp'],
      role: json['role'] ?? 'staff',
      avatar: json['avatar'],
      departmentId: json['department_id'],
      departmentName: deptName,
      isHrdOrAdmin: json['is_hrd_or_admin'] ?? false,
      isApproved: json['is_approved'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'whatsapp_number': whatsappNumber,
      'national_id_ktp': nationalIdKtp,
      'role': role,
      'avatar': avatar,
      'department_id': departmentId,
      'department_name': departmentName,
      'is_hrd_or_admin': isHrdOrAdmin,
      'is_approved': isApproved,
    };
  }
}
