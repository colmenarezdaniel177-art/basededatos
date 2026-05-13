<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedir Cita</title>
    <link rel="stylesheet" href="pedir_cita.css">
</head>
<body>

    <div class="contenedor">
        <div class="formulario-cita">

            <h1>Pedir Cita</h1>

            <form action="" method="POST">

                <div class="grupo">
                    <label>Doctor</label>

                    <select name="doctor" required>
                        <option value="">Seleccione un doctor</option>
                        <option value="doctor1">Dr. Juan Pérez</option>
                        <option value="doctor2">Dra. María López</option>
                        <option value="doctor3">Dr. Carlos Gómez</option>
                    </select>
                </div>

                <div class="grupo">
                    <label>Motivo de Consulta</label>

                    <textarea 
                        name="motivo" 
                        rows="5" 
                        placeholder="Escriba el motivo de su consulta..."
                        required>
                    </textarea>
                </div>

                <div class="grupo">
                    <label>Fechas Disponibles</label>

                    <input type="date" name="fecha" required>
                </div>

                <div class="botones">

                    <button type="reset" class="btn limpiar">
                        Limpiar
                    </button>

                    <button type="button" class="btn cancelar"
                        onclick="window.location.href='../index.php'">
                        Cancelar
                    </button>

                    <button type="submit" class="btn aceptar">
                        Aceptar
                    </button>

                </div>

            </form>

        </div>
    </div>

</body>
</html>