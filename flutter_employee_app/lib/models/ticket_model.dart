class TicketModel {
  final int id;
  final String title;
  final String category;
  final String priority;
  final String status;
  final String description;
  final String? photoUrl;
  final String? createdAt;
  final String? createdAtHuman;
  final String? senderName;
  final String? senderAvatar;
  final int? targetDepartmentId;
  final String? targetDepartmentName;
  final String? targetDepartmentIcon;
  final int messagesCount;

  TicketModel({
    required this.id,
    required this.title,
    required this.category,
    required this.priority,
    required this.status,
    required this.description,
    this.photoUrl,
    this.createdAt,
    this.createdAtHuman,
    this.senderName,
    this.senderAvatar,
    this.targetDepartmentId,
    this.targetDepartmentName,
    this.targetDepartmentIcon,
    this.messagesCount = 0,
  });

  factory TicketModel.fromJson(Map<String, dynamic> json) {
    String? sName;
    String? sAvatar;
    if (json['sender'] != null && json['sender'] is Map) {
      sName = json['sender']['name'];
      sAvatar = json['sender']['avatar'];
    }

    int? deptId = json['target_department_id'];
    String? deptName;
    String? deptIcon;
    if (json['target_department'] != null && json['target_department'] is Map) {
      deptId ??= json['target_department']['id'];
      deptName = json['target_department']['name'];
      deptIcon = json['target_department']['icon'];
    }

    return TicketModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      category: json['category'] ?? '',
      priority: json['priority'] ?? 'Medium',
      status: json['status'] ?? 'Pending',
      description: json['description'] ?? '',
      photoUrl: json['photo_url'],
      createdAt: json['created_at'],
      createdAtHuman: json['created_at_human'],
      senderName: sName,
      senderAvatar: sAvatar,
      targetDepartmentId: deptId,
      targetDepartmentName: deptName,
      targetDepartmentIcon: deptIcon,
      messagesCount: json['messages_count'] ?? 0,
    );
  }
}
