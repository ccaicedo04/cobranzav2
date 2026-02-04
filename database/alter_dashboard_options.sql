ALTER TABLE configuracion_colegio
  ADD COLUMN dashboard_top_responsables INT(11) DEFAULT 5 AFTER twilio_incoming_webhook,
  ADD COLUMN dashboard_meses_cartera INT(11) DEFAULT 6 AFTER dashboard_top_responsables;
