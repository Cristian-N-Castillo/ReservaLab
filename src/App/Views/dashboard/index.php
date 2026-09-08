<?php

declare(strict_types=1);

use Core\Session;

/**
 * @var int $totalUsuarios
 * @var int $totalLaboratorios
 * @var int $totalReservas
 * @var int $totalCursos
 * @var int $totalHorarios
 * @var array $proximasReservas
 * @var \DateTimeImmutable $mesAgenda
 * @var array $reservasPorDiaAgenda
 * @var int $cuposPorDia
 */

$nombre = Session::get('nombre', 'Administrador');

date_default_timezone_set('America/Santiago');

$proximasReservas = $proximasReservas ?? [];
$reservasPorDiaAgenda = $reservasPorDiaAgenda ?? [];
$cuposPorDia = max(1, (int) ($cuposPorDia ?? 1));

/**
 * Nivel de ocupación de un día, para pintarlo en el mini calendario.
 *
 * Se mide contra los cupos totales del día (laboratorios × bloques):
 * hasta un tercio es holgado, hasta dos tercios es medio, y de ahí
 * en adelante queda poco disponible.
 */
function nivelOcupacionAgenda(int $reservas, int $cupos): string
{
    if ($reservas <= 0) {
        return '';
    }

    $porcentaje = $reservas / $cupos;

    if ($porcentaje < 0.34) {
        return 'baja';
    }

    if ($porcentaje < 0.67) {
        return 'media';
    }

    return 'alta';
}
$mesAgenda = $mesAgenda ?? new DateTimeImmutable('first day of this month');

$dias = [
    'Sunday'    => 'Domingo',
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sábado'
];

$meses = [
    1  => 'enero', 2  => 'febrero', 3  => 'marzo', 4  => 'abril',
    5  => 'mayo', 6  => 'junio', 7  => 'julio', 8  => 'agosto',
    9  => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];

$fechaActual = sprintf(
    '%s %d de %s de %d',
    $dias[date('l')],
    (int) date('d'),
    $meses[(int) date('n')],
    (int) date('Y')
);

$hoy = date('Y-m-d');

function fechaDashboard(string $fecha): string
{
    $timestamp = strtotime($fecha);

    return $timestamp === false ? $fecha : date('d/m/Y', $timestamp);
}

function horaDashboard(?string $hora): string
{
    return empty($hora) ? '' : substr($hora, 0, 5);
}

?>

<div class="container-fluid">

    <!-- ============================================================= -->
    <!-- BIENVENIDA -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-12 col-md-8">

                    <h2 class="fw-bold mb-2">
                        👋 Bienvenido, <?= htmlspecialchars((string) $nombre) ?>
                    </h2>

                </div>

                <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">

                    <h6 class="text-secondary mb-1">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?= htmlspecialchars($fechaActual) ?>
                    </h6>

                    <h4 class="text-primary mb-0">
                        <i class="bi bi-clock me-1"></i>
                        <?= date('H:i') ?>
                    </h4>

                </div>

            </div>

        </div>

    </div>


    <div class="row g-4">

        <!-- ========================================================= -->
        <!-- MOSAICO DE ACCESOS + LABORATORIOS -->
        <!-- ========================================================= -->

        <div class="col-12 col-xl-8">

            <div class="mosaico mb-4">

                <a href="/usuarios" class="mosaico-tile tile-azul">

                    <div class="mosaico-icono">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div>
                        <div class="mosaico-numero"><?= (int) $totalUsuarios ?></div>
                        <div class="mosaico-titulo">Usuarios</div>
                        <div class="mosaico-detalle">Registrados</div>
                    </div>

                </a>

                <a href="/laboratorios" class="mosaico-tile tile-verde">

                    <div class="mosaico-icono">
                        <i class="bi bi-pc-display"></i>
                    </div>

                    <div>
                        <div class="mosaico-numero"><?= (int) $totalLaboratorios ?></div>
                        <div class="mosaico-titulo">Laboratorios</div>
                        <div class="mosaico-detalle">Activos</div>
                    </div>

                </a>

                <a href="/cursos" class="mosaico-tile tile-morado">

                    <div class="mosaico-icono">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>

                    <div>
                        <div class="mosaico-numero"><?= (int) $totalCursos ?></div>
                        <div class="mosaico-titulo">Cursos</div>
                        <div class="mosaico-detalle">Creados</div>
                    </div>

                </a>

                <a href="/horarios" class="mosaico-tile tile-naranja">

                    <div class="mosaico-icono">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>
                        <div class="mosaico-numero"><?= (int) $totalHorarios ?></div>
                        <div class="mosaico-titulo">Horarios</div>
                        <div class="mosaico-detalle">Bloques del día</div>
                    </div>

                </a>

                <a href="/reservas" class="mosaico-tile tile-celeste tile-ancha">

                    <div class="mosaico-icono">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>

                    <div>
                        <div class="mosaico-numero"><?= (int) $totalReservas ?></div>
                        <div class="mosaico-titulo">Reservas de hoy</div>
                        <div class="mosaico-detalle">
                            Confirmadas &middot; crear una nueva reserva
                        </div>
                    </div>

                </a>

                <a href="/reservas/calendario" class="mosaico-tile tile-rosado tile-ancha">

                    <div class="mosaico-icono">
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <div>
                        <div class="mosaico-titulo">Calendario</div>
                        <div class="mosaico-detalle">
                            Vista mensual de todas las reservas
                        </div>
                    </div>

                </a>

                <a href="/observaciones" class="mosaico-tile tile-lavanda tile-ancha">

                    <div class="mosaico-icono">
                        <i class="bi bi-journal-text"></i>
                    </div>

                    <div>
                        <div class="mosaico-titulo">Observaciones</div>
                        <div class="mosaico-detalle">
                            Registro de incidencias en los laboratorios
                        </div>
                    </div>

                </a>

                <a href="/reportes" class="mosaico-tile tile-amarillo tile-ancha">

                    <div class="mosaico-icono">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>

                    <div>
                        <div class="mosaico-titulo">Reportes</div>
                        <div class="mosaico-detalle">
                            Estadísticas de uso por laboratorio y docente
                        </div>
                    </div>

                </a>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- AGENDA -->
        <!-- ========================================================= -->

        <div class="col-12 col-xl-4">

            <div class="card dashboard-card h-100">

                <div class="card-header">
                    <i class="bi bi-calendar3 me-1"></i>
                    Agenda
                </div>

                <div class="card-body">

                    <?php

                    /*
                     * Mini calendario del mes en curso: sirve para ubicarse,
                     * marcando hoy y los días que tienen reservas.
                     */
                    $ultimoDiaMes = $mesAgenda->modify('last day of this month');

                    $inicioGrilla = $mesAgenda->modify(
                        '-' . ((int) $mesAgenda->format('N') - 1) . ' days'
                    );

                    $finGrilla = $ultimoDiaMes->modify(
                        '+' . (7 - (int) $ultimoDiaMes->format('N')) . ' days'
                    );

                    ?>

                    <div class="fw-semibold text-capitalize mb-3">
                        <?= htmlspecialchars($meses[(int) $mesAgenda->format('n')]) ?>
                        <?= htmlspecialchars($mesAgenda->format('Y')) ?>
                    </div>

                    <div class="agenda-mes">

                        <?php foreach (['Lu', 'Ma', 'Mi', 'Ju', 'Vi'] as $diaSemana): ?>

                            <div class="agenda-cabecera">
                                <?= htmlspecialchars($diaSemana) ?>
                            </div>

                        <?php endforeach; ?>

                        <?php

                        $cursor = $inicioGrilla;

                        while ($cursor <= $finGrilla):

                            // No se reserva fin de semana: esos días no se pintan.
                            if ((int) $cursor->format('N') > 5) {
                                $cursor = $cursor->modify('+1 day');
                                continue;
                            }

                            $fechaTexto = $cursor->format('Y-m-d');

                            $reservasDelDia = (int) ($reservasPorDiaAgenda[$fechaTexto] ?? 0);

                            $nivel = nivelOcupacionAgenda($reservasDelDia, $cuposPorDia);

                            $clases = ['agenda-dia'];

                            if ($cursor->format('n') !== $mesAgenda->format('n')) {
                                $clases[] = 'agenda-dia-fuera';
                            }

                            if ($nivel !== '') {
                                $clases[] = 'agenda-dia-ocupacion';
                                $clases[] = 'agenda-ocupacion-' . $nivel;
                            }

                            if ($fechaTexto === $hoy) {
                                $clases[] = 'agenda-dia-hoy';
                            }

                            $titulo = $reservasDelDia === 0
                                ? 'Sin reservas'
                                : $reservasDelDia . ' de ' . $cuposPorDia . ' bloques reservados';

                            ?>

                            <div
                                class="<?= implode(' ', $clases) ?>"
                                title="<?= htmlspecialchars(
                                    date('d/m/Y', strtotime($fechaTexto)) . ' — ' . $titulo
                                ) ?>">

                                <span class="agenda-numero">
                                    <?= (int) $cursor->format('j') ?>
                                </span>

                            </div>

                            <?php

                            $cursor = $cursor->modify('+1 day');

                        endwhile;

                        ?>

                    </div>

                    <!-- Qué significa el color del punto bajo cada día -->
                    <div class="agenda-leyenda mt-3">

                        <span>
                            <span class="agenda-punto agenda-ocupacion-baja"></span>
                            Holgado
                        </span>

                        <span>
                            <span class="agenda-punto agenda-ocupacion-media"></span>
                            Medio
                        </span>

                        <span>
                            <span class="agenda-punto agenda-ocupacion-alta"></span>
                            Casi lleno
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PRÓXIMAS RESERVAS -->
    <!-- ============================================================= -->

    <div class="row g-4 mt-1">

        <div class="col-12">
            <div class="card dashboard-card">

                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div>
                        <i class="bi bi-calendar-week me-1"></i>
                        Próximas Reservas
                    </div>

                    <?php if (!empty($proximasReservas)): ?>

                        <span class="badge bg-primary">
                            <?= count($proximasReservas) ?>
                        </span>

                    <?php endif; ?>

                </div>

                <div class="card-body p-0">

                    <?php if (empty($proximasReservas)): ?>

                        <div class="empty-state py-5">

                            <i class="bi bi-calendar-x"></i>

                            <h5>No hay reservas programadas</h5>

                            <p class="mb-3">Cuando existan reservas aparecerán aquí.</p>

                            <a href="/reservas" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-calendar-plus me-1"></i>
                                Crear reserva
                            </a>

                        </div>

                    <?php else: ?>

                        <div class="list-group list-group-flush" id="listaProximas">

                            <?php foreach ($proximasReservas as $reserva): ?>

                                <?php

                                $estado = mb_strtolower((string) ($reserva['estado'] ?? ''));

                                $badgeEstado = match ($estado) {
                                    'pendiente'  => 'bg-warning text-dark',
                                    'confirmada' => 'bg-success',
                                    'finalizada' => 'bg-secondary',
                                    'cancelada'  => 'bg-danger',
                                    default      => 'bg-secondary'
                                };

                                $urlAgenda = '/reservas?' . http_build_query([
                                    'fecha' => (string) ($reserva['fecha'] ?? ''),
                                    'id_laboratorio' => (int) ($reserva['id_laboratorio'] ?? 0)
                                ]);

                                ?>

                                <div class="list-group-item px-4 py-3" data-reserva>

                                    <div class="row align-items-center g-3">

                                        <!-- Fecha -->
                                        <div class="col-6 col-md-2">

                                            <div class="text-primary fw-bold">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= htmlspecialchars(
                                                    fechaDashboard((string) ($reserva['fecha'] ?? ''))
                                                ) ?>
                                            </div>

                                        </div>

                                        <!-- Horario -->
                                        <div class="col-6 col-md-2">

                                            <div class="fw-semibold">
                                                <?= htmlspecialchars(
                                                    (string) ($reserva['horario'] ?? 'Bloque')
                                                ) ?>
                                            </div>

                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                <?= htmlspecialchars(
                                                    horaDashboard($reserva['hora_inicio'] ?? null)
                                                ) ?>
                                                -
                                                <?= htmlspecialchars(
                                                    horaDashboard($reserva['hora_fin'] ?? null)
                                                ) ?>
                                            </small>

                                        </div>

                                        <!-- Laboratorio / Curso -->
                                        <div class="col-6 col-md-3">

                                            <div class="fw-semibold">
                                                <i class="bi bi-pc-display me-1"></i>
                                                <?= htmlspecialchars(
                                                    (string) ($reserva['laboratorio'] ?? '')
                                                ) ?>
                                            </div>

                                            <small class="text-muted">
                                                <i class="bi bi-mortarboard me-1"></i>
                                                <?= htmlspecialchars(
                                                    (string) ($reserva['curso'] ?? '')
                                                ) ?>
                                            </small>

                                        </div>

                                        <!-- Responsable -->
                                        <div class="col-6 col-md-3">

                                            <small class="text-muted d-block">Responsable</small>

                                            <span>
                                                <?= htmlspecialchars(trim(
                                                    ($reserva['nombres'] ?? '') . ' ' . ($reserva['apellidos'] ?? '')
                                                )) ?>
                                            </span>

                                        </div>

                                        <!-- Estado / acción -->
                                        <div class="col-12 col-md-2 text-md-end">

                                            <span class="badge <?= $badgeEstado ?> mb-2">
                                                <?= htmlspecialchars((string) ($reserva['estado'] ?? '')) ?>
                                            </span>

                                            <div>
                                                <a
                                                    href="<?= htmlspecialchars($urlAgenda) ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Ver en agenda">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <nav class="px-4 pt-3">
                            <ul id="paginacionProximas" class="pagination pagination-sm justify-content-center mb-0"></ul>
                        </nav>

                        <div class="p-3 border-top text-end">

                            <a href="/reservas/calendario" class="btn btn-sm btn-outline-primary">
                                Ver calendario
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

/*
 * Paginación de "Próximas Reservas": se muestran de a 3 para que la
 * tarjeta no se alargue. Es solo presentación, todas las reservas ya
 * vienen renderizadas en el HTML.
 */
document.addEventListener('DOMContentLoaded', () => {

    const POR_PAGINA = 3;

    const lista = document.getElementById('listaProximas');
    const paginacion = document.getElementById('paginacionProximas');

    if (!lista || !paginacion) {
        return;
    }

    const filas = Array.from(lista.querySelectorAll('[data-reserva]'));

    const totalPaginas = Math.max(1, Math.ceil(filas.length / POR_PAGINA));

    let paginaActual = 1;

    function crearItemPaginacion(etiqueta, pagina, deshabilitado, activo) {

        const li = document.createElement('li');

        li.className = 'page-item'
            + (deshabilitado ? ' disabled' : '')
            + (activo ? ' active' : '');

        const enlace = document.createElement('a');
        enlace.className = 'page-link';
        enlace.href = '#';
        enlace.textContent = etiqueta;

        enlace.addEventListener('click', (evento) => {

            evento.preventDefault();

            if (!deshabilitado) {
                paginaActual = pagina;
                renderizar();
            }

        });

        li.appendChild(enlace);

        return li;

    }

    function renderizar() {

        const inicio = (paginaActual - 1) * POR_PAGINA;
        const fin = inicio + POR_PAGINA;

        filas.forEach((fila, indice) => {
            fila.classList.toggle('d-none', indice < inicio || indice >= fin);
        });

        paginacion.innerHTML = '';

        if (totalPaginas <= 1) {
            return;
        }

        paginacion.appendChild(
            crearItemPaginacion('Anterior', paginaActual - 1, paginaActual === 1, false)
        );

        for (let pagina = 1; pagina <= totalPaginas; pagina++) {

            paginacion.appendChild(
                crearItemPaginacion(String(pagina), pagina, false, pagina === paginaActual)
            );

        }

        paginacion.appendChild(
            crearItemPaginacion('Siguiente', paginaActual + 1, paginaActual === totalPaginas, false)
        );

    }

    renderizar();

});

</script>
