import 'package:flutter/material.dart';
import '../models/message_model.dart';
import '../models/ticket_model.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class HrdTicketDetailScreen extends StatefulWidget {
  final int ticketId;
  const HrdTicketDetailScreen({super.key, required this.ticketId});

  @override
  State<HrdTicketDetailScreen> createState() => _HrdTicketDetailScreenState();
}

class _HrdTicketDetailScreenState extends State<HrdTicketDetailScreen> {
  final _messageController = TextEditingController();
  final _scrollController = ScrollController();

  TicketModel? _ticket;
  List<MessageModel> _messages = [];
  UserModel? _currentUser;
  bool _isLoading = true;
  bool _isSending = false;

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

  Future<void> _updateStatus(String newStatus) async {
    final res = await ApiService.updateTicketStatus(widget.ticketId, newStatus);
    if (!mounted) return;
    if (res['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Status diperbarui menjadi $newStatus')),
      );
      _loadTicketDetails();
    }
  }

  Future<void> _sendMessage() async {
    final text = _messageController.text.trim();
    if (text.isEmpty) return;

    setState(() => _isSending = true);

    final res = await ApiService.sendMessage(widget.ticketId, text);

    if (!mounted) return;

    setState(() => _isSending = false);

    if (res['success'] == true) {
      _messageController.clear();
      setState(() {
        _messages.add(res['message']);
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
          _ticket != null ? 'Penanganan Tiket #${_ticket!.id}' : 'Detail Tiket',
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
            icon: const Icon(Icons.refresh_rounded, color: Color(0xFF4F46E5)),
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
                    // Ticket & Employee Info Header
                    Container(
                      color: Colors.white,
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Pengaju: ${_ticket!.senderName ?? 'Karyawan'}',
                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF0F172A)),
                                  ),
                                  if (_ticket!.senderWhatsapp != null)
                                    Text(
                                      'WA: ${_ticket!.senderWhatsapp}',
                                      style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                                    ),
                                ],
                              ),
                              _statusPill(_ticket!.status),
                            ],
                          ),
                          const SizedBox(height: 10),
                          Text(
                            _ticket!.title,
                            style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: Color(0xFF0F172A)),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            _ticket!.description,
                            style: const TextStyle(fontSize: 12, color: Color(0xFF475569)),
                          ),
                          const SizedBox(height: 12),

                          // Quick Status Action Buttons for HRD
                          Row(
                            children: [
                              if (_ticket!.status != 'In Progress')
                                Expanded(
                                  child: OutlinedButton.icon(
                                    onPressed: () => _updateStatus('In Progress'),
                                    icon: const Icon(Icons.sync_rounded, size: 16),
                                    label: const Text('Proses Tiket', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                    style: OutlinedButton.styleFrom(
                                      foregroundColor: const Color(0xFF0284C7),
                                      side: const BorderSide(color: Color(0xFF38BDF8)),
                                      padding: const EdgeInsets.symmetric(vertical: 8),
                                    ),
                                  ),
                                ),
                              if (_ticket!.status != 'In Progress' && _ticket!.status != 'Resolved') const SizedBox(width: 8),
                              if (_ticket!.status != 'Resolved')
                                Expanded(
                                  child: ElevatedButton.icon(
                                    onPressed: () => _updateStatus('Resolved'),
                                    icon: const Icon(Icons.check_circle_rounded, size: 16),
                                    label: const Text('Tandai Selesai', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: const Color(0xFF10B981),
                                      foregroundColor: Colors.white,
                                      padding: const EdgeInsets.symmetric(vertical: 8),
                                    ),
                                  ),
                                ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    const Divider(height: 1, color: Color(0xFFE2E8F0)),

                    // Conversation Stream
                    Expanded(
                      child: ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                        itemCount: _messages.length,
                        itemBuilder: (ctx, index) {
                          final msg = _messages[index];
                          final isHrdResponse = msg.isAdmin || (msg.senderRole == 'admin') || (_currentUser != null && msg.senderId == _currentUser!.id);
                          return _chatBubble(msg, isHrdResponse);
                        },
                      ),
                    ),

                    // Reply Input Box
                    Container(
                      color: Colors.white,
                      padding: const EdgeInsets.fromLTRB(14, 8, 14, 12),
                      child: SafeArea(
                        child: Row(
                          children: [
                            Expanded(
                              child: TextField(
                                controller: _messageController,
                                decoration: InputDecoration(
                                  hintText: 'Ketik tanggapan / solusi ke karyawan...',
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
                            const SizedBox(width: 8),
                            CircleAvatar(
                              backgroundColor: const Color(0xFF4F46E5),
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
                      ),
                    ),
                  ],
                ),
    );
  }

  Widget _chatBubble(MessageModel msg, bool isHrdResponse) {
    return Align(
      alignment: isHrdResponse ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: isHrdResponse ? const Color(0xFF4F46E5) : Colors.white,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(isHrdResponse ? 16 : 4),
            bottomRight: Radius.circular(isHrdResponse ? 4 : 16),
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
          crossAxisAlignment: isHrdResponse ? CrossAxisAlignment.end : CrossAxisAlignment.start,
          children: [
            Text(
              '${msg.senderName ?? 'Karyawan'} (${msg.senderDepartment ?? 'Divisi'})',
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: isHrdResponse ? const Color(0xFFC7D2FE) : const Color(0xFF4F46E5),
              ),
            ),
            const SizedBox(height: 3),
            Text(
              msg.message,
              style: TextStyle(
                fontSize: 13,
                color: isHrdResponse ? Colors.white : const Color(0xFF0F172A),
              ),
            ),
            const SizedBox(height: 4),
            Text(
              msg.createdAtHuman ?? '',
              style: TextStyle(
                fontSize: 9,
                color: isHrdResponse ? Colors.white70 : const Color(0xFF94A3B8),
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
