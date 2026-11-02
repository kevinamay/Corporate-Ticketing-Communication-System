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
  final int? senderId;
  final String? senderName;
  final String? senderEmail;
  final String? senderWhatsapp;
  final String? senderAvatar;
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
    this.senderId,
    this.senderName,
    this.senderEmail,
    this.senderWhatsapp,
    this.senderAvatar,
    this.targetDepartmentName,
    this.targetDepartmentIcon,
    this.messagesCount = 0,
  });

  factory TicketModel.fromJson(Map<String, dynamic> json) {
    int? sId;
    String? sName;
    String? sEmail;
    String? sWa;
    String? sAvatar;
    if (json['sender'] != null && json['sender'] is Map) {
      sId = json['sender']['id'];
      sName = json['sender']['name'];
      sEmail = json['sender']['email'];
      sWa = json['sender']['whatsapp_number'];
      sAvatar = json['sender']['avatar'];
    }

    String? deptName;
    String? deptIcon;
    if (json['target_department'] != null && json['target_department'] is Map) {
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
      senderId: sId,
      senderName: sName,
      senderEmail: sEmail,
      senderWhatsapp: sWa,
      senderAvatar: sAvatar,
      targetDepartmentName: deptName,
      targetDepartmentIcon: deptIcon,
      messagesCount: json['messages_count'] ?? 0,
    );
  }
}
