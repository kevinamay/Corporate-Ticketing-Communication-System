import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../models/message_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class TicketDetailScreen extends StatefulWidget {
  final int ticketId;
  const TicketDetailScreen({super.key, required this.ticketId});

  @override
  State<TicketDetailScreen> createState() => _TicketDetailScreenState();
}

class _TicketDetailScreenState extends State<TicketDetailScreen> {
  final _messageController = TextEditingController();
  final _scrollController = ScrollController();
  final ImagePicker _picker = ImagePicker();

  TicketModel? _ticket;
  List<MessageModel> _messages = [];
  UserModel? _currentUser;
  bool _isLoading = true;
  bool _isSending = false;
  File? _replyPhoto;
  String? _replyBase64;

  @override
  void initState() {
    super.initState();
    _loadTicketDetails();
  }

  @override
  void dispose() {
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _loadTicketDetails() async {
    final user = await ApiService.getSavedUser();
    final res = await ApiService.getTicketDetail(widget.ticketId);

    if (!mounted) return;

    if (res['success'] == true) {
      setState(() {
        _currentUser = user;
        _ticket = res['ticket'];
        _messages = res['messages'];
        _isLoading = false;
      });
      _scrollToBottom();
    } else {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(res['message'] ?? 'Gagal memuat detail tiket')),
      );
    }
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      }
    });
  }

  Future<void> _pickReplyPhoto(ImageSource source) async {
    try {
      final picked = await _picker.pickImage(source: source, maxWidth: 1000, maxHeight: 1000, imageQuality: 80);
      if (picked != null) {
        final file = File(picked.path);
        final bytes = await file.readAsBytes();
        setState(() {
          _replyPhoto = file;
          _replyBase64 = base64Encode(bytes);
        });
      }
    } catch (_) {}
  }

  Future<void> _sendMessage() async {
    final text = _messageController.text.trim();
    if (text.isEmpty && _replyBase64 == null) return;

    setState(() => _isSending = true);

    final res = await ApiService.sendMessage(
      widget.ticketId,
      text.isEmpty ? 'Lampiran foto' : text,
      photoBase64: _replyBase64,
    );

    if (!mounted) return;

    setState(() => _isSending = false);

    if (res['success'] == true) {
      _messageController.clear();
      setState(() {
        _replyPhoto = null;
        _replyBase64 = null;
        _messages.add(res['message']);
        if (res['updated_ticket_status'] != null && _ticket != null) {
          _ticket = TicketModel(
            id: _ticket!.id,
            title: _ticket!.title,
            category: _ticket!.category,
            priority: _ticket!.priority,
            status: res['updated_ticket_status'],
            description: _ticket!.description,
            photoUrl: _ticket!.photoUrl,
            createdAt: _ticket!.createdAt,
            createdAtHuman: _ticket!.createdAtHuman,
            senderName: _ticket!.senderName,
            senderAvatar: _ticket!.senderAvatar,
            targetDepartmentName: _ticket!.targetDepartmentName,
            targetDepartmentIcon: _ticket!.targetDepartmentIcon,
            messagesCount: _messages.length,
          );
        }
      });
      _scrollToBottom();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(res['message_text'] ?? 'Gagal mengirim pesan')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        title: Text(
          _ticket != null ? 'Tiket #${_ticket!.id}' : 'Detail Tiket',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF0F172A)),
        ),
        backgroundColor: Colors.white,
        elevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: Color(0xFF0F172A)),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: Color(0xFF2563EB)),
            onPressed: _loadTicketDetails,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _ticket == null
              ? const Center(child: Text('Tiket tidak ditemukan'))
              : Column(
                  children: [
                    // Ticket Metadata Header Card
                    Container(
                      color: Colors.white,
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFEFF6FF),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  _ticket!.targetDepartmentName ?? 'IT Support',
                                  style: const TextStyle(color: Color(0xFF1D4ED8), fontSize: 10, fontWeight: FontWeight.bold),
                                ),
                              ),
                              _statusPill(_ticket!.status),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            _ticket!.title,
                            style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFF0F172A)),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            _ticket!.description,
                            style: const TextStyle(fontSize: 12, color: Color(0xFF475569)),
                          ),
                          if (_ticket!.photoUrl != null) ...[
                            const SizedBox(height: 8),
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: Image.network(
                                _ticket!.photoUrl!,
                                height: 120,
                                width: double.infinity,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => const SizedBox.shrink(),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                    const Divider(height: 1, color: Color(0xFFE2E8F0)),

                    // Chat Conversation Thread
                    Expanded(
                      child: ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        itemCount: _messages.length,
                        itemBuilder: (ctx, index) {
                          final msg = _messages[index];
                          final isMe = _currentUser != null && msg.senderId == _currentUser!.id;
                          return _chatBubble(msg, isMe);
                        },
                      ),
                    ),

                    // Reply Composer Bar
                    Container(
                      color: Colors.white,
                      padding: const EdgeInsets.fromLTRB(10, 8, 10, 12),
                      child: SafeArea(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            if (_replyPhoto != null)
                              Padding(
                                padding: const EdgeInsets.only(bottom: 6),
                                child: Row(
                                  children: [
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(8),
                                      child: Image.file(_replyPhoto!, width: 44, height: 44, fit: BoxFit.cover),
                                    ),
                                    const SizedBox(width: 8),
                                    const Text('Lampiran gambar siap dikirim', style: TextStyle(fontSize: 11, color: Color(0xFF64748B))),
                                    const Spacer(),
                                    IconButton(
                                      icon: const Icon(Icons.close, size: 16),
                                      onPressed: () => setState(() {
                                        _replyPhoto = null;
                                        _replyBase64 = null;
                                      }),
                                    ),
                                  ],
                                ),
                              ),
                            Row(
                              children: [
                                IconButton(
                                  icon: const Icon(Icons.add_photo_alternate_outlined, color: Color(0xFF2563EB)),
                                  onPressed: () => _pickReplyPhoto(ImageSource.gallery),
                                ),
                                Expanded(
                                  child: TextField(
                                    controller: _messageController,
                                    decoration: InputDecoration(
                                      hintText: 'Tulis balasan atau pesan teknis...',
                                      hintStyle: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
                                      filled: true,
                                      fillColor: const Color(0xFFF1F5F9),
                                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(20), borderSide: BorderSide.none),
                                      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                                    ),
                                    minLines: 1,
                                    maxLines: 3,
                                  ),
                                ),
                                const SizedBox(width: 6),
                                CircleAvatar(
                                  backgroundColor: const Color(0xFF2563EB),
                                  radius: 20,
                                  child: _isSending
                                      ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                      : IconButton(
                                          icon: const Icon(Icons.send_rounded, color: Colors.white, size: 18),
                                          onPressed: _sendMessage,
                                        ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
    );
  }

  Widget _chatBubble(MessageModel msg, bool isMe) {
    return Align(
      alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: isMe ? const Color(0xFF2563EB) : Colors.white,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(isMe ? 16 : 4),
            bottomRight: Radius.circular(isMe ? 4 : 16),
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: isMe ? CrossAxisAlignment.end : CrossAxisAlignment.start,
          children: [
            if (!isMe)
              Text(
                '${msg.senderName ?? 'Staf'} (${msg.senderDepartment ?? 'Support'})',
                style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF2563EB)),
              ),
            if (!isMe) const SizedBox(height: 2),
            Text(
              msg.message,
              style: TextStyle(
                fontSize: 13,
                color: isMe ? Colors.white : const Color(0xFF0F172A),
              ),
            ),
            if (msg.photoUrl != null) ...[
              const SizedBox(height: 6),
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: Image.network(
                  msg.photoUrl!,
                  height: 120,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => const SizedBox.shrink(),
                ),
              ),
            ],
            const SizedBox(height: 4),
            Text(
              msg.createdAtHuman ?? '',
              style: TextStyle(
                fontSize: 9,
                color: isMe ? Colors.white70 : const Color(0xFF94A3B8),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _statusPill(String status) {
    Color bg = const Color(0xFFFEF3C7);
    Color text = const Color(0xFFB45309);
    if (status == 'In Progress') {
      bg = const Color(0xFFEFF6FF);
      text = const Color(0xFF1D4ED8);
    } else if (status == 'Resolved') {
      bg = const Color(0xFFECFDF5);
      text = const Color(0xFF047857);
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(8)),
      child: Text(status, style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: text)),
    );
  }
}
