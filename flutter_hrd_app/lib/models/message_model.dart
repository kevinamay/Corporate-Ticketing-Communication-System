class MessageModel {
  final int id;
  final int ticketId;
  final String message;
  final String? photoUrl;
  final String? createdAt;
  final String? createdAtHuman;
  final int? senderId;
  final String? senderName;
  final String? senderRole;
  final String? senderAvatar;
  final String? senderDepartment;
  final bool isAdmin;

  MessageModel({
    required this.id,
    required this.ticketId,
    required this.message,
    this.photoUrl,
    this.createdAt,
    this.createdAtHuman,
    this.senderId,
    this.senderName,
    this.senderRole,
    this.senderAvatar,
    this.senderDepartment,
    this.isAdmin = false,
  });

  factory MessageModel.fromJson(Map<String, dynamic> json) {
    int? sId;
    String? sName;
    String? sRole;
    String? sAvatar;
    String? sDept;
    bool isAdm = false;

    if (json['sender'] != null && json['sender'] is Map) {
      sId = json['sender']['id'];
      sName = json['sender']['name'];
      sRole = json['sender']['role'];
      sAvatar = json['sender']['avatar'];
      sDept = json['sender']['department_name'];
      isAdm = json['sender']['is_admin'] ?? false;
    }

    return MessageModel(
      id: json['id'] ?? 0,
      ticketId: json['ticket_id'] ?? 0,
      message: json['message'] ?? '',
      photoUrl: json['photo_url'],
      createdAt: json['created_at'],
      createdAtHuman: json['created_at_human'],
      senderId: sId,
      senderName: sName,
      senderRole: sRole,
      senderAvatar: sAvatar,
      senderDepartment: sDept,
      isAdmin: isAdm,
    );
  }
}
