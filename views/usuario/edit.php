<?php
$title = "Editar Usuario";
require_once '../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fas fa-users me-2"></i></i>Editar Usuario
            </h4>
            <a href="index.php?controller=especialista&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?controller=usuario&action=edit&id=<?php echo $usuario['id']; ?>" id="usuarioForm">
                <div class="row">
                    <div class="col-md-6">
                        <label for="login" class="form-label">Nombre de Usuario *</label>
                        <input type="text" class="form-control" id="login" name="login"
                            value="<?php echo htmlspecialchars($usuario['login']); ?>"
                            required maxlength="50" readonly>
                        <div class="form-text">
                            El nombre de usuario no puede ser modificado.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rol_id" class="form-label">Rol *</label>
                            <select class="form-select" id="rol_id" name="rol_id" required>
                                <option value="">Seleccione un rol</option>
                                <?php if (!empty($roles)): ?>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol['id']; ?>"
                                            <?php echo $rol['id'] == $usuario['rol_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay roles disponibles</option>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">
                                <?php if (empty($roles)): ?>
                                    <span class="text-danger">Debe crear roles primero</span>
                                <?php else: ?>
                                    Seleccione el rol del usuario.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_hash" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password_hash" name="password_hash"
                                minlength="6" placeholder="Dejar en blanco para no cambiar">
                            <div class="form-text">Complete solo si desea cambiar la contraseña.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                placeholder="Repita la nueva contraseña">
                            <div class="form-text" id="passwordFeedback">
                                Las contraseñas deben coincidir si se cambian.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_password">
                        <label class="form-check-label" for="show_password">
                            Mostrar contraseñas
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?controller=usuario&action=index" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    function validatePasswords() {
        const password = document.getElementById('password_hash').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const feedback = document.getElementById('passwordFeedback');
        const submitBtn = document.getElementById('submitBtn');

        // Si ambos campos están vacíos, no hay problema
        if (password === '' && confirmPassword === '') {
            feedback.innerHTML = '<span class="text-info">La contraseña no se cambiará</span>';
            submitBtn.disabled = false;
            return true;
        }

        // Si solo uno está lleno, mostrar error
        if ((password === '' && confirmPassword !== '') || (password !== '' && confirmPassword === '')) {
            feedback.innerHTML = '<span class="text-warning">Complete ambos campos para cambiar la contraseña</span>';
            submitBtn.disabled = true;
            return false;
        }

        // Si ambos están llenos, validar coincidencia
        if (password !== confirmPassword) {
            feedback.innerHTML = '<span class="text-danger">Las contraseñas no coinciden</span>';
            submitBtn.disabled = true;
            return false;
        }

        // Validar longitud mínima
        if (password.length < 6) {
            feedback.innerHTML = '<span class="text-danger">La contraseña debe tener al menos 6 caracteres</span>';
            submitBtn.disabled = true;
            return false;
        }

        feedback.innerHTML = '<span class="text-success">Las contraseñas coinciden</span>';
        submitBtn.disabled = false;
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Validar contraseñas en tiempo real
        document.getElementById('password').addEventListener('input', validatePasswords);
        document.getElementById('confirm_password').addEventListener('input', validatePasswords);

        // Mostrar/ocultar contraseñas
        document.getElementById('show_password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('confirm_password');
            const type = this.checked ? 'text' : 'password';

            passwordField.type = type;
            confirmField.type = type;
        });

        // Validar formulario antes de enviar
        document.getElementById('usuarioForm').addEventListener('submit', function(e) {
            if (!validatePasswords()) {
                e.preventDefault();
                alert('Por favor, corrija los errores en el formulario.');
                return false;
            }
        });

        // Validar inicialmente
        validatePasswords();
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>