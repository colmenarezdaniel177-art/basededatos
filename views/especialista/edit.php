<?php
$title = "Editar Especialista";
require_once '../views/layouts/header.php';
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();
$query = "SELECT id, nombre FROM especialidad ORDER BY nombre ASC";
$stmt = $db->query($query);
$listaEspecialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fas fa-user-md me-2"></i>Editar Especialista
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
            <form method="POST" action="index.php?controller=especialista&action=edit&id=<?php echo $especialista['id']; ?>">
                <div class="row">
                    <div class="mb-9">
                        <label for="nombre;" class="form-label">Nombre de Especialista *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            required maxlength="50" placeholder="Ingrese el nombre de especialista"
                            value="<?php echo htmlspecialchars($especialista['nombre']); ?>" required>
                        <div class="form-text" id="usernameFeedback">
                            El nombre de especialista debe ser único.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3">
                        <label for="especialidad" class="form-label">Especialidad *</label>
                        <select class="form-select" id="especialidad_id" name="especialidad_id" required>
                            <option value="">Seleccione una especialidad</option>
                            
                            <?php foreach ($listaEspecialidades as $esp): ?>
                                <?php 
                                    $selected = ($esp['id'] == $especialista['especialidad_id']) ? 'selected' : ''; 
                                ?>
                                <option value="<?php echo $esp['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($esp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            Seleccione la especialidad del profesional
                        </div>
                    </div>
                </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="index.php?controller=especialista&action=index" class="btn btn-outline-secondary px-4">
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



<?php require_once '../views/layouts/footer.php'; ?>