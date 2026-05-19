<?php
$title = "Atención de Consulta";
require_once '../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-user-nurse me-2"></i>Detalles de la Consulta</h4>
                <span class="badge bg-light text-primary">Cita #<?php echo $cita['id']; ?></span>
            </div>
            <div class="card-body">
                <div class="row mb-4 p-3 bg-light rounded">
                    <div class="col-md-4">
                        <label class="text-muted d-block">Paciente</label>
                        <strong><?php echo htmlspecialchars($cita['paciente_nombre']); ?></strong>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted d-block">Especialista</label>
                        <strong><?php echo htmlspecialchars($cita['especialista_nombre']); ?></strong>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted d-block">Fecha</label>
                        <strong><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></strong>
                    </div>
                </div>

                <form method="POST" action="index.php?controller=cita&action=guardarConsulta">
                    <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Motivo de la Consulta</label>
                            <textarea class="form-control" name="motivo_consulta" rows="2" placeholder="Describa el motivo..."></textarea>
                        </div>                        
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Diagnóstico / Tratamiento</label>
                            <textarea class="form-control" name="diagnostico" rows="4" required placeholder="Medicamentos o indicaciones"></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Observaciones adicionales</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>

                  <div class="col-md-12 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Tratamiento / Indicaciones Médicas</label>
                                <button type="button" id="btn-agregar-medicamento" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Agregar Medicamento
                                </button>
                            </div>
                            
                            <div id="contenedor-tratamiento" class="border rounded p-3 bg-light-subtle">
                                <div class="row g-2 fila-medicamento mb-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="small text-muted">Medicamento</label>
                                        <select class="form-select" name="medicamentos[]" required>
                                            <option value="">Seleccione un Medicamento</option>
                                            <?php if (!empty($medicamentos)): ?>
                                                <?php foreach ($medicamentos as $m): ?>
                                                    <option value="<?php echo $m['id']; ?>">
                                                        <?php echo htmlspecialchars($m['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No hay medicamentos disponibles</option>
                                            <?php endif; ?>
                                        </select>                                        
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted">Indicaciones</label>
                                        <input type="text" name="indicaciones[]" class="form-control form-control-sm" required placeholder="Ej: Cada 8 horas">
                                    </div>
                                    
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-fila w-100" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 border-top pt-3">
                        <a href="index.php?controller=cita&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Finalizar Consulta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contenedor = document.getElementById('contenedor-tratamiento');
    const btnAgregar = document.getElementById('btn-agregar-medicamento');

    const listaMedicamentos = <?php echo json_encode($medicamentos ?? []); ?>;
    function generarOpciones() {
        alert(JSON.stringify(listaMedicamentos, null, 2));
        console.log("Lista de medicamentos:", listaMedicamentos);
        let html = '<option value="">Seleccione un Medicamento</option>';
        if (Array.isArray(listaMedicamentos)) {
            listaMedicamentos.forEach(function(m) {
                html += `<option value="${m.id}">${m.nombre}</option>`;
            });
        }
        return html;
    }
         


    // Evento para agregar una nueva fila
    btnAgregar.addEventListener('click', function() {
        const nuevaFila = document.createElement('div');
        nuevaFila.className = 'row g-2 fila-medicamento mb-2 align-items-end';
        
       nuevaFila.innerHTML = `
            <div class="col-md-4">
                <label class="small text-muted">Medicamento</label>
                <select class="form-select" name="medicamentos[]" required>
                    ${generarOpciones()}
                </select>                                       
            </div>
            <div class="col-md-3">
                <label class="small text-muted">Indicaciones</label>
                <input type="text" name="indicaciones[]" class="form-control form-control-sm" required placeholder="Ej: Cada 8 horas">
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-fila w-100" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    contenedor.appendChild(nuevaFila);
    });

    // Evento delegado para eliminar filas (así funciona incluso para las filas creadas dinámicamente)
    contenedor.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-eliminar-fila') || e.target.closest('.btn-eliminar-fila')) {
            const filas = contenedor.querySelectorAll('.fila-medicamento');
            
            // Opcional: Evitar borrar si es la única fila que queda
            if (filas.length > 1) {
                const filaAELiminar = e.target.closest('.fila-medicamento');
                filaAELiminar.remove();
            } else {
                alert('La consulta debe tener al menos una indicación o medicamento.');
            }
        }
    });
});
</script>

<?php require_once '../views/layouts/footer.php'; ?>