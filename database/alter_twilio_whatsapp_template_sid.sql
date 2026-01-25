ALTER TABLE configuracion_colegio
  ADD COLUMN twilio_whatsapp_template_sid VARCHAR(64) DEFAULT NULL
  AFTER twilio_whatsapp_from;
