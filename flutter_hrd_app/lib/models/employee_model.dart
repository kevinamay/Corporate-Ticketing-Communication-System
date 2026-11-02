class EmployeeModel {
  final int id;
  final String name;
  final String email;
  final String? whatsappNumber;
  final String? nationalIdKtp;
  final String? gender;
  final String? completeAddress;
  final int? departmentId;
  final String departmentName;
  final bool isApproved;
  final String? approvedAt;
  final String? avatar;
  final String? createdAtHuman;

  EmployeeModel({
    required this.id,
    required this.name,
    required this.email,
    this.whatsappNumber,
    this.nationalIdKtp,
    this.gender,
    this.completeAddress,
    this.departmentId,
    required this.departmentName,
    required this.isApproved,
    this.approvedAt,
    this.avatar,
    this.createdAtHuman,
  });

  factory EmployeeModel.fromJson(Map<String, dynamic> json) {
    return EmployeeModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      whatsappNumber: json['whatsapp_number'],
      nationalIdKtp: json['national_id_ktp'],
      gender: json['gender'],
      completeAddress: json['complete_address'],
      departmentId: json['department_id'],
      departmentName: json['department_name'] ?? 'Umum',
      isApproved: json['is_approved'] ?? false,
      approvedAt: json['approved_at'],
      avatar: json['avatar'],
      createdAtHuman: json['created_at_human'],
    );
  }

  EmployeeModel copyWith({bool? isApproved}) {
    return EmployeeModel(
      id: id,
      name: name,
      email: email,
      whatsappNumber: whatsappNumber,
      nationalIdKtp: nationalIdKtp,
      gender: gender,
      completeAddress: completeAddress,
      departmentId: departmentId,
      departmentName: departmentName,
      isApproved: isApproved ?? this.isApproved,
      approvedAt: approvedAt,
      avatar: avatar,
      createdAtHuman: createdAtHuman,
    );
  }
}
