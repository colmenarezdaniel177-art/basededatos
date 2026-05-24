USE clinica_db;

-- ── Rol médico ──────────────────────────────────────────────
INSERT IGNORE INTO roles (nombre) VALUES ('medico');

-- ── Status de cita (reemplaza ENUM) ─────────────────────────
CREATE TABLE IF NOT EXISTS status_cita (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  color  VARCHAR(30) DEFAULT 'secondary',
  activo TINYINT(1) DEFAULT 1
);
INSERT IGNORE INTO status_cita (nombre, color) VALUES
  ('Pendiente',  'warning'),
  ('Confirmada', 'info'),
  ('Completada', 'success'),
  ('Cancelada',  'danger');

-- ── Tipo de antecedente (reemplaza ENUM) ─────────────────────
CREATE TABLE IF NOT EXISTS tipo_antecedente (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  color  VARCHAR(30) DEFAULT 'secondary',
  activo TINYINT(1) DEFAULT 1
);
INSERT IGNORE INTO tipo_antecedente (nombre, color) VALUES
  ('Personal',      'primary'),
  ('Familiar',      'secondary'),
  ('Quirurgico',    'danger'),
  ('Alergico',      'warning'),
  ('Farmacologico', 'info');

-- ── Horarios de especialista ─────────────────────────────────
CREATE TABLE IF NOT EXISTS horarios_especialista (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  especialistaId INT NOT NULL,
  dia_semana    TINYINT(1) DEFAULT NULL,   -- 0=Dom 1=Lun … 6=Sab
  hora_inicio   TIME DEFAULT NULL,
  hora_fin      TIME DEFAULT NULL,
  FOREIGN KEY (especialistaId) REFERENCES especialistas(id) ON DELETE CASCADE
);

-- ── Agregar status_id a citas ────────────────────────────────
-- (El script setup_update.php ejecuta estos ALTER con manejo de errores)
-- ALTER TABLE citas ADD COLUMN status_id INT AFTER motivo;
-- UPDATE citas c JOIN status_cita s ON c.estado = s.nombre SET c.status_id = s.id;
-- ALTER TABLE citas ADD CONSTRAINT fk_citas_status FOREIGN KEY (status_id) REFERENCES status_cita(id);
-- ALTER TABLE citas DROP COLUMN hora;

-- ── Agregar tipo_id a antecedentes ───────────────────────────
-- ALTER TABLE antecedentes ADD COLUMN tipo_id INT AFTER paciente_id;
-- UPDATE antecedentes a JOIN tipo_antecedente t ON a.tipo = t.nombre SET a.tipo_id = t.id;
-- ALTER TABLE antecedentes ADD CONSTRAINT fk_ant_tipo FOREIGN KEY (tipo_id) REFERENCES tipo_antecedente(id);
