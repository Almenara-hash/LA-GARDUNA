// app.js - Funciones globales para toda la web

document.addEventListener('DOMContentLoaded', () => {

    /* ============================================================
       1) Confirmaciones para acciones peligrosas (borrar / cancelar)
       Cualquier enlace con data-confirm activará la ventana de confirmación.
    ============================================================ */
    const enlacesConfirmacion = document.querySelectorAll('[data-confirm]');

    enlacesConfirmacion.forEach(enlace => {
        enlace.addEventListener('click', (e) => {
            const mensaje = enlace.dataset.confirm || "¿Seguro que deseas continuar?";
            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });



    /* ============================================================
       2) Auto-ocultar alertas con clase 'alert-auto'
       IMPORTANTE: No afecta al horario del index (alert normal)
    ============================================================ */
    const alerts = document.querySelectorAll('.alert.alert-auto');

    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";

                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);
    }



    /* ============================================================
       3) Evitar fechas pasadas al crear disponibilidad
    ============================================================ */
    const fechaInput = document.querySelector('input[name="nueva_fecha"]');

    if (fechaInput) {
        const hoy = new Date();
        const año = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
        const dia = String(hoy.getDate()).padStart(2, '0');

        fechaInput.min = `${año}-${mes}-${dia}`;
    }



    /* ============================================================
       4) Validación frontal del horario permitido
          - Mañana: 10:00 a 14:00
          - Tarde: 17:00 a 20:30
    ============================================================ */
    const inputHora = document.querySelector('input[name="nueva_hora"]');

    if (inputHora) {
        inputHora.addEventListener('change', () => {

            const h = inputHora.value;

            const rangoManana = (h >= "10:00" && h <= "14:00");
            const rangoTarde  = (h >= "17:00" && h <= "20:30");

            if (!rangoManana && !rangoTarde) {
                alert("⛔ Horario no válido.\n\nDisponible:\n10:00–14:00\n17:00–20:30");
                inputHora.value = "";
            }
        });
    }

});
