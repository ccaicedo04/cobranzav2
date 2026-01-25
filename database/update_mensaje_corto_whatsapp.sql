UPDATE plantilla_comunicacion
SET cuerpo_html = 'Hola {{responsable_nombre}}, te escribimos del {{colegio_nombre}}.\nEl saldo pendiente de {{estudiante_nombre}} es de {{saldo_pendiente}} con vencimiento {{fecha_vencimiento}}.\nSi ya realizaste el pago, por favor ignora este mensaje.',
    variables = 'responsable_nombre,colegio_nombre,estudiante_nombre,saldo_pendiente,fecha_vencimiento'
WHERE nombre = 'Mensaje corto WhatsApp'
  AND canal = 'whatsapp';
