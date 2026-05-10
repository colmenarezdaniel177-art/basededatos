<?php
$title = "Antecedentes del Paciente";
require_once '../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-history text-primary"></i> Historial de Antecedentes</h3>
    <a href="index.php?controller=paciente&action=index" class="btn btn-outline-secondary">Volver a Pacientes</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Nuevo Antecedente</div>
            <div class="card-body">
                <form action="index.php?controller=paciente_antecedente&action=store" method="POST">
                    <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
                    
                    <div class="mb-3">
                        <label>Tipo</label>
                        <select name="tipo_antecedente_id" class="form-select" required>
                            <?php while($t = $tipos->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo $t['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Descripción / Observación</label>
                        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Guardar Antecedente</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($antecedentes->rowCount() > 0): ?>
                            <?php while($row = $antecedentes->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['fecha']; ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo $row['tipo_nombre']; ?></span></td>
                                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td>
                                    <a href="index.php?controller=paciente_antecedente&action=delete&id=<?php echo $row['id']; ?>&paciente_id=<?php echo $paciente_id; ?>" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="return confirm('¿Está seguro de eliminar este antecedente?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No hay antecedentes registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>