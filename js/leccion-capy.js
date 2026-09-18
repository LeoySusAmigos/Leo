document.addEventListener('DOMContentLoaded', () => {

    const page = document.querySelector('.capy-leccion-page');

    if (!page) {
        return;
    }

    const cards = [...page.querySelectorAll('.actividad-card')];
    const total = cards.length;

    if (total === 0) {
        return;
    }

    const progress = document.getElementById('progressFill');
    const currentLabel = document.getElementById('actividadActual');

    let current = Math.max(
        1,
        Math.min(
            total,
            parseInt(
                page.dataset.actividadInicial || '1',
                10
            ) || 1
        )
    );

    let points = parseInt(
        page.dataset.puntosIniciales || '0',
        10
    ) || 0;

    const porcentajeGuardado = Math.max(
        0,
        Math.min(
            100,
            parseInt(
                page.dataset.porcentajeInicial || '0',
                10
            ) || 0
        )
    );

    /*
    * =========================================
    * PROGRESO HISTÓRICO
    * =========================================
    */

    const cantidadCompletadasGuardadas = Math.min(
        total,
        Math.round(
            porcentajeGuardado / 100 * total
        )
    );

    const completed = new Set();

    for (
        let i = 1;
        i <= cantidadCompletadasGuardadas;
        i++
    ) {
        completed.add(i);
    }

    if (
        page.dataset.leccionCompletada === '1' ||
        porcentajeGuardado >= 100
    ) {
        for (let i = 1; i <= total; i++) {
            completed.add(i);
        }
    }

    let audioActual = null;

    /*
     * =========================================
     * UTILIDADES
     * =========================================
     */

    const normalize = value => {

        return String(value || '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');

    };


    const getCurrentCard = () => {

        return cards[current - 1] || null;

    };


    const isActivityComplete = card => {

        if (!card) {
            return false;
        }

        const number = Number(
            card.dataset.actividad || 0
        );

        return (
            card.dataset.resuelta === '1' ||
            completed.has(number)
        );
    };


    const shuffle = container => {

        if (!container) {
            return;
        }

        const items = [...container.children];

        for (
            let i = items.length - 1;
            i > 0;
            i--
        ) {

            const randomIndex =
                Math.floor(
                    Math.random() * (i + 1)
                );

            [
                items[i],
                items[randomIndex]
            ] = [
                items[randomIndex],
                items[i]
            ];

        }

        items.forEach(item => {

            container.appendChild(item);

        });

    };

    function prepararArrastreSustantivos(card) {

        const zona = card.querySelector(
            '.zona-sustantivos'
        );

        if (!zona) {
            return;
        }

        const lista = card.querySelector(
            '.sustantivos-lista'
        );

        if (!lista) {
            return;
        }

        const elementos = [
            ...lista.querySelectorAll(
                '.sustantivo-arrastrable'
            )
        ];

        const contenidoZona = zona.querySelector(
            '.zona-sustantivos-contenido'
        );

        if (!contenidoZona) {
            return;
        }


        /*
        * =========================================
        * ELEMENTOS CORRECTOS
        * =========================================
        */
        let elementosCorrectos =
            elementos.filter(elemento => {

                const valor =
                    elemento.dataset.correcta;

                return (
                    valor === undefined ||
                    valor === '' ||
                    valor === '1'
                );

            });


        /*
        * Compatibilidad:
        */

        if (
            elementosCorrectos.length === 0 &&
            elementos.length > 0
        ) {
            elementosCorrectos = elementos;
        }

        /*
        * =========================================
        * PLACEHOLDER
        * =========================================
        */

        function actualizarPlaceholder() {

            const placeholder =
                contenidoZona.querySelector(
                    '.zona-sustantivos-placeholder'
                );

            const colocados =
                contenidoZona.querySelectorAll(
                    '.sustantivo-colocado'
                );

            if (placeholder) {

                placeholder.hidden =
                    colocados.length > 0;

            }
        }


        /*
        * =========================================
        * COMPROBAR COMPLETITUD
        * =========================================
        */

        function completarSiCorresponde() {

            const colocados =
                contenidoZona.querySelectorAll(
                    '.sustantivo-colocado'
                );

            if (
                colocados.length !==
                elementosCorrectos.length
            ) {
                return;
            }


            /*
            * Verificamos que todos los colocados
            * correspondan realmente a elementos correctos.
            */

            const todosCorrectos =
                [...colocados].every(colocado => {

                    const id =
                        colocado.dataset.sustantivoId;

                    const original =
                        elementos.find(
                            elemento =>
                                elemento.dataset.sustantivoId === id
                        );

                    if (!original) {
                        return false;
                    }

                    const correcta =
                        original.dataset.correcta;

                    return (
                        correcta === undefined ||
                        correcta === '' ||
                        correcta === '1'
                    );

                });


            if (!todosCorrectos) {
                return;
            }


            zona.classList.add(
                'completada'
            );


            const resultado =
                card.querySelector(
                    '.resultado-sustantivos'
                );

            if (resultado) {
                resultado.hidden = false;
            }


            /*
            * IMPORTANTE:
            * aquí pasamos la tarjeta completa,
            * no el número de actividad.
            */

            completeActivity(card);

            updateNavigation();

        }


        /*
        * =========================================
        * COLOCAR SUSTANTIVO
        * =========================================
        */

        function colocarSustantivo(elemento) {

            if (
                !elemento ||
                elemento.classList.contains('usado')
            ) {
                return;
            }


            const texto =
                elemento.dataset.texto || '';


            if (!texto) {
                return;
            }


            /*
            * Si tiene explícitamente data-correcta="0",
            * no permitimos colocarlo.
            */

            if (
                elemento.dataset.correcta === '0'
            ) {

                showFeedback(
                    card,
                    'Esa palabra no es un sustantivo. Inténtalo de nuevo.',
                    false
                );

                return;
            }


            const colocado =
                document.createElement('button');


            colocado.type = 'button';

            colocado.className =
                'sustantivo-colocado';


            colocado.dataset.sustantivoId =
                elemento.dataset.sustantivoId || '';


            colocado.dataset.texto =
                texto;


            colocado.innerHTML = `
                <i class="fa-solid fa-check"></i>
                <span>${texto}</span>
            `;


            /*
            * Permite retirar una palabra colocada
            * para volver a intentarlo.
            */

            colocado.addEventListener(
                'click',
                function () {

                    elemento.classList.remove(
                        'usado'
                    );

                    elemento.hidden = false;

                    colocado.remove();

                    zona.classList.remove(
                        'completada'
                    );


                    const resultado =
                        card.querySelector(
                            '.resultado-sustantivos'
                        );

                    if (resultado) {
                        resultado.hidden = true;
                    }


                    /*
                    * Esto solamente cambia el estado
                    * visual de la actividad.
                    *
                    * NO eliminamos `completed`.
                    */

                    card.dataset.resuelta = '0';


                    actualizarPlaceholder();

                    updateNavigation();

                }
            );


            contenidoZona.appendChild(
                colocado
            );


            elemento.classList.add(
                'usado'
            );

            elemento.hidden = true;


            actualizarPlaceholder();

            completarSiCorresponde();

        }


        /*
        * =========================================
        * DRAG
        * =========================================
        */

        elementos.forEach(elemento => {

            elemento.addEventListener(
                'dragstart',
                function (event) {

                    if (
                        elemento.classList.contains(
                            'usado'
                        )
                    ) {
                        return;
                    }

                    elemento.classList.add(
                        'arrastrando'
                    );

                    event.dataTransfer.effectAllowed =
                        'move';

                    event.dataTransfer.setData(
                        'text/plain',
                        elemento.dataset.sustantivoId || ''
                    );

                }
            );


            elemento.addEventListener(
                'dragend',
                function () {

                    elemento.classList.remove(
                        'arrastrando'
                    );

                }
            );


            /*
            * También permitimos clic.
            * Esto es útil especialmente en dispositivos
            * donde arrastrar puede ser incómodo.
            */

            elemento.addEventListener(
                'click',
                function () {

                    colocarSustantivo(
                        elemento
                    );

                }
            );

        });


        /*
        * =========================================
        * DROP
        * =========================================
        */

        zona.addEventListener(
            'dragover',
            function (event) {

                event.preventDefault();

                zona.classList.add(
                    'arrastre-activo'
                );

                event.dataTransfer.dropEffect =
                    'move';

            }
        );


        zona.addEventListener(
            'dragleave',
            function (event) {

                if (
                    !zona.contains(
                        event.relatedTarget
                    )
                ) {

                    zona.classList.remove(
                        'arrastre-activo'
                    );

                }

            }
        );


        zona.addEventListener(
            'drop',
            function (event) {

                event.preventDefault();

                zona.classList.remove(
                    'arrastre-activo'
                );


                const id =
                    event.dataTransfer.getData(
                        'text/plain'
                    );


                if (!id) {
                    return;
                }


                const elemento =
                    elementos.find(
                        item =>
                            item.dataset.sustantivoId === id
                    );


                if (!elemento) {
                    return;
                }


                colocarSustantivo(
                    elemento
                );

            }
        );


        actualizarPlaceholder();

    }


    const showFeedback = (
        card,
        message,
        correct = true
    ) => {

        if (!card) {
            return;
        }

        const feedback =
            card.querySelector(
                '.actividad-feedback'
            );

        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.hidden = false;

        feedback.className =
            'actividad-feedback ' +
            (
                correct
                    ? 'correcto'
                    : 'incorrecto'
            );

    };

    const saveProgress = () => {

        const porcentajeActual = Math.round(
            completed.size /
            Math.max(total, 1) *
            100
        );

        const completada =
            completed.size === total
                ? 1
                : 0;

        const data = {

            leccion_id:
                page.dataset.leccionId,

            actividad_actual:
                current,

            porcentaje:
                porcentajeActual,

            puntos:
                points,

            completada:
                completada

        };


        return fetch(
            'php/guardar-progreso-capy.php',
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json'
                },

                body: JSON.stringify(data)

            }
        )
        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Error HTTP al guardar progreso.'
                );

            }

            return response.json();

        })
        .catch(() => {

            return null;

        });

    };


    /*
     * =========================================
     * NAVEGACIÓN
     * =========================================
     */

    const updateNavigation = () => {

        const card = getCurrentCard();

        if (!card) {
            return;
        }

        const activityComplete =
            isActivityComplete(card);


        const nextButton =
            card.querySelector(
                '.btn-siguiente'
            );

        if (nextButton) {

            nextButton.disabled =
                !activityComplete;

        }


        const previousButton =
            card.querySelector(
                '.btn-anterior'
            );

        if (previousButton) {

            previousButton.disabled =
                current <= 1;

        }

    };


    const showActivity = number => {

        current = Math.max(
            1,
            Math.min(
                total,
                number
            )
        );


        /*
        * =========================================
        * MOSTRAR / OCULTAR TARJETAS
        * =========================================
        */

        cards.forEach(
            (card, index) => {

                const cardNumber =
                    index + 1;

                const isCurrent =
                    cardNumber === current;


                card.hidden =
                    !isCurrent;


                card.classList.toggle(
                    'activa',
                    isCurrent
                );


                card.setAttribute(
                    'aria-hidden',
                    String(!isCurrent)
                );

            }
        );


        /*
        * =========================================
        * LIMPIAR VISUALMENTE LA ACTIVIDAD
        * =========================================
        *
        * La actividad puede estar completada
        * históricamente, pero al volver a entrar
        * queremos permitir una nueva práctica.
        */

        const activeCard =
            getCurrentCard();


        if (activeCard) {
            resetCard(
                activeCard,
                true
            );
        }


        /*
        * =========================================
        * BARRA DE PROGRESO
        * =========================================
        */

        if (progress) {

            const porcentajeActual =
                Math.round(
                    completed.size /
                    Math.max(total, 1) *
                    100
                );

            progress.style.width =
                porcentajeActual + '%';

        }


        /*
        * =========================================
        * NÚMERO DE ACTIVIDAD
        * =========================================
        */

        if (currentLabel) {

            currentLabel.textContent =
                current;

        }


        updateNavigation();


        /*
        * Guardamos solamente la posición actual.
        * El progreso histórico no se modifica aquí.
        */

        saveProgress();

    };


    /*
     * =========================================
     * COMPLETAR ACTIVIDAD
     * =========================================
     */

    const completeActivity = card => {

        if (!card) {
            return;
        }


        const number =
            Number(
                card.dataset.actividad || 0
            );


        if (number <= 0) {
            return;
        }


        /*
        * Solo otorgamos los puntos la primera vez
        * que la actividad entra al progreso histórico.
        */

        if (
            !completed.has(number)
        ) {

            completed.add(number);

            points += Number(
                card.dataset.puntos || 0
            );

        }


        /*
        * Estado visual de este intento.
        */

        card.dataset.resuelta = '1';


        card.classList.add(
            'actividad-completada'
        );


        updateNavigation();


        /*
        * El progreso se guarda, pero repetir una
        * actividad no vuelve a sumar puntos.
        */

        saveProgress();

    };


    /*
     * =========================================
     * AUDIO
     * =========================================
     */

    const playAudio = url => {

        if (!url) {
            return;
        }


        try {

            if (audioActual) {

                audioActual.pause();

                audioActual.currentTime = 0;

            }


            audioActual =
                new Audio(url);

            audioActual.play()
                .catch(() => {});

        } catch (error) {}

    };


    /*
     * =========================================
     * OBTENER ORDEN DE UNA OPCIÓN
     * =========================================
     *
     * Tus datos utilizan principalmente:
     *
     * data-orden-correcto
     *
     * pero algunas versiones pueden tener:
     *
     * data-orden
     *
     * Por eso soportamos ambas.
     */

    const getOrder = element => {

        if (!element) {
            return null;
        }

        const value =
            element.dataset.ordenCorrecto ??
            element.dataset.orden ??
            '';

        if (value === '') {
            return null;
        }

        const number =
            Number(value);

        return Number.isFinite(number)
            ? number
            : null;

    };

    const esActividad4Leccion1 = card => {

        if (!card) {
            return false;
        }

        const leccionId =
            String(
                page.dataset.leccionId || ''
            ).trim();

        const numeroActividad =
            Number(
                card.dataset.actividad || 0
            );

        return (
            leccionId === '1' &&
            numeroActividad === 4
        );
    };

    /*
    * =========================================
    * ACTIVIDAD 5 - LECCIÓN 1
    * RECONOCER SUSTANTIVOS
    * =========================================
    */

    const esActividad5Leccion1 = card => {

        if (!card) {
            return false;
        }

        const leccionId =
            String(
                page.dataset.leccionId || ''
            ).trim();

        const numeroActividad =
            Number(
                card.dataset.actividad || 0
            );

        return (
            leccionId === '1' &&
            numeroActividad === 5
        );
    };

    /*
    * =========================================
    * ACTIVIDAD 5 - SELECCIÓN MÚLTIPLE
    * =========================================
    */

    page.addEventListener(
        'click',
        event => {

            const option =
                event.target.closest(
                    '.opcion-capy'
                );


            if (!option) {
                return;
            }


            const card =
                option.closest(
                    '.actividad-card'
                );


            if (
                !card ||
                !esActividad5Leccion1(card)
            ) {
                return;
            }


            event.stopImmediatePropagation();


            if (
                card.dataset.resuelta === '1'
            ) {
                return;
            }


            const data =
                prepararConstruccionSeleccion(
                    card
                );


            if (!data) {
                return;
            }


            option.classList.toggle(
                'seleccionada'
            );


            /*
            * Cuando selecciona una opción
            * correcta, damos una pequeña
            * confirmación visual.
            */
            if (
                option.classList.contains(
                    'seleccionada'
                )
            ) {

                if (
                    option.dataset.correcta === '1'
                ) {

                    showFeedback(
                        card,
                        '¡Bien! Sigue buscando los sustantivos.',
                        true
                    );

                } else {

                    showFeedback(
                        card,
                        'Esa palabra no es un sustantivo. Revisa las demás.',
                        false
                    );

                }

            }


            comprobarConstruccionSeleccion(
                card
            );

        },
        true
    );

    /*
    * =========================================
    * ACTIVIDAD 4 - LECCIÓN 1
    * SELECCIÓN DEL SUSTANTIVO
    * =========================================
    *
    * Esta actividad es una excepción:
    * no ordena palabras, solo selecciona
    * cuál es el sustantivo correcto.
    */

    page.addEventListener(
        'click',
        event => {

            const word =
                event.target.closest(
                    '.palabra-orden'
                );


            if (!word) {
                return;
            }


            const card =
                word.closest(
                    '.actividad-card'
                );


            if (
                !card ||
                !esActividad4Leccion1(card)
            ) {
                return;
            }


            /*
            * Evitamos que el manejador genérico
            * de "ordenar" procese esta actividad.
            */
            event.stopImmediatePropagation();


            /*
            * Si la actividad ya está resuelta,
            * no hacemos nada.
            */
            if (
                card.dataset.resuelta === '1'
            ) {
                return;
            }


            /*
            * Limpiamos cualquier estado
            * visual anterior.
            */
            card.querySelectorAll(
                '.palabra-orden'
            ).forEach(
                opcion => {

                    opcion.classList.remove(
                        'correcto',
                        'incorrecto',
                        'seleccionado'
                    );

                }
            );


            /*
            * =================================
            * RESPUESTA CORRECTA
            * =================================
            */

            if (
                word.dataset.correcta === '1'
            ) {

                word.classList.add(
                    'correcto'
                );

                word.style.backgroundColor =
                    '#e8f7e3';

                word.style.borderColor =
                    '#6ebe45';

                word.style.color =
                    '#2f8b44';

                word.style.boxShadow =
                    '0 0 0 2px rgba(110, 190, 69, 0.15)';

                word.style.transform =
                    'translateY(-1px)';


                completeActivity(
                    card
                );


                showFeedback(
                    card,
                    '¡Correcto! Gato es un sustantivo porque nombra a un animal.',
                    true
                );


                return;
            }


            /*
            * =================================
            * RESPUESTA INCORRECTA
            * =================================
            */

            word.classList.add(
                'incorrecto'
            );


            showFeedback(
                card,
                'Esa palabra no es un sustantivo. Inténtalo de nuevo.',
                false
            );


            setTimeout(
                () => {

                    word.classList.remove(
                        'incorrecto'
                    );

                },
                500
            );

        },
        true
    );

    /*
     * =========================================
     * CONSTRUCCIÓN DE ORACIONES
     * =========================================
     */

    const prepararConstruccion = card => {

        const type =
        normalize(
            card.dataset.tipo
        );


        if (type !== 'construir') {
            return;
        }


        const options =
            card.querySelector(
                '.opciones-container'
            );


        if (!options) {
            return;
        }


        const opcionesOrdenadas =
            [
                ...options.querySelectorAll(
                    '.opcion-capy'
                )
            ]
            .filter(
                element =>
                    getOrder(element) !== null
            );


        /*
         * Si existen órdenes verdaderos,
         * usamos construcción por secuencia.
         */
        if (
            opcionesOrdenadas.length > 0
        ) {

            let zone =
                card.querySelector(
                    '.zona-construccion'
                );


            if (!zone) {

                zone =
                    document.createElement(
                        'div'
                    );

                zone.className =
                    'zona-oracion zona-construccion';

                zone.setAttribute(
                    'aria-label',
                    'Oración construida'
                );


                const label =
                    document.createElement(
                        'div'
                    );

                label.className =
                    'zona-construccion-titulo';

                label.textContent =
                    'Forma aquí tu oración:';


                zone.appendChild(label);


                options.parentNode.insertBefore(
                    zone,
                    options
                );

            }

        }

    };


    /*
     * =========================================
     * CONSTRUCCIÓN POR ORDEN
     * =========================================
     */

    const agregarPalabraConstruccion = (
        card,
        option
    ) => {

        if (!card || !option) {
            return false;
        }


        const order =
            getOrder(option);


        if (order === null) {
            return false;
        }


        let zone =
            card.querySelector(
                '.zona-construccion'
            );


        if (!zone) {

            prepararConstruccion(card);

            zone =
                card.querySelector(
                    '.zona-construccion'
                );

        }


        if (!zone) {
            return false;
        }


        if (
            option.classList.contains(
                'usada'
            )
        ) {
            return true;
        }


        const clone =
            option.cloneNode(true);


        clone.classList.remove(
            'opcion-capy',
            'usada',
            'correcto',
            'incorrecto'
        );


        clone.classList.add(
            'palabra-colocada'
        );


        clone.dataset.source =
            option.dataset.opcionId ||
            option.dataset.id ||
            '';


        zone.appendChild(
            clone
        );


        option.classList.add(
            'usada'
        );


        comprobarConstruccion(
            card
        );


        return true;

    };


    const comprobarConstruccion = card => {

        if (!card) {
            return;
        }


        const zone =
            card.querySelector(
                '.zona-construccion'
            );


        if (!zone) {
            return;
        }


        const selectedWords =
            [
                ...zone.querySelectorAll(
                    '.palabra-colocada'
                )
            ];


        const expectedWords =
            [
                ...card.querySelectorAll(
                    '.opciones-container .opcion-capy'
                )
            ]
            .filter(
                element =>
                    getOrder(element) !== null
            )
            .sort(
                (a, b) =>
                    getOrder(a) -
                    getOrder(b)
            );


        if (
            expectedWords.length === 0
        ) {
            return;
        }


        if (
            selectedWords.length <
            expectedWords.length
        ) {

            return;

        }


        if (
            selectedWords.length >
            expectedWords.length
        ) {

            return;

        }


        const isCorrect =
            selectedWords.every(
                (
                    element,
                    index
                ) => {

                    const selectedText =
                        normalize(
                            element.textContent
                        );

                    const expectedText =
                        normalize(
                            expectedWords[
                                index
                            ].textContent
                        );

                    return (
                        selectedText ===
                        expectedText
                    );

                }
            );


        if (isCorrect) {

            completeActivity(
                card
            );


            showFeedback(
                card,
                '¡Excelente! Construiste la oración correctamente.',
                true
            );

        } else {

            showFeedback(
                card,
                'Revisa el orden de las palabras.',
                false
            );

        }

    };


    /*
     * =========================================
     * CONSTRUCCIÓN SIN ORDEN
     * =========================================
     *
     * Cuando los datos solamente tienen
     * varias opciones con es_correcta = 1,
     * tratamos la actividad como selección
     * de todas las palabras correctas.
     */

    const prepararConstruccionSeleccion = card => {

        const options =
            card.querySelector(
                '.opciones-container'
            );


        if (!options) {
            return null;
        }


        const allOptions =
            [
                ...options.querySelectorAll(
                    '.opcion-capy'
                )
            ];


        const ordered =
            allOptions.some(
                element =>
                    getOrder(element) !== null
            );


        if (ordered) {
            return null;
        }


        const correctOptions =
            allOptions.filter(
                element =>
                    element.dataset.correcta === '1'
            );


        /*
         * Si hay más de una respuesta correcta,
         * esta actividad funciona como selección múltiple.
         */
        if (
            correctOptions.length <= 1
        ) {
            return null;
        }


        return {
            allOptions,
            correctOptions
        };

    };


    const comprobarConstruccionSeleccion = card => {

        const data =
            prepararConstruccionSeleccion(
                card
            );


        if (!data) {
            return;
        }


        const {
            allOptions,
            correctOptions
        } = data;


        const seleccionadas =
            allOptions.filter(
                option =>
                    option.classList.contains(
                        'seleccionada'
                    )
            );


        /*
         * Si seleccionó una incorrecta,
         * todavía no completamos.
         */
        const hayIncorrecta =
            seleccionadas.some(
                option =>
                    option.dataset.correcta !== '1'
            );


        if (hayIncorrecta) {
            return;
        }


        /*
         * Deben estar seleccionadas
         * exactamente todas las correctas.
         */
        if (
            seleccionadas.length !==
            correctOptions.length
        ) {
            return;
        }


        const todasCorrectas =
            correctOptions.every(
                option =>
                    option.classList.contains(
                        'seleccionada'
                    )
            );


        if (!todasCorrectas) {
            return;
        }


        completeActivity(
            card
        );


        showFeedback(
            card,
            '¡Excelente! Elegiste todas las palabras correctas.',
            true
        );

    };


    /*
     * =========================================
     * REINICIAR ACTIVIDAD
     * =========================================
     */

    const resetCard = (
        card,
        desdeNavegacion = false
    ) => {

        if (!card) {
            return;
        }


        /*
        * =========================================
        * ESTADO VISUAL ACTUAL
        * =========================================
        *
        * SIEMPRE comenzamos la práctica limpia.
        *
        * `completed` NO se toca.
        */

        card.dataset.resuelta = '0';


        card.classList.remove(
            'actividad-completada'
        );

        card.querySelectorAll(
            '.palabra-orden'
        ).forEach(
            opcion => {

                opcion.style.backgroundColor = '';

                opcion.style.borderColor = '';

                opcion.style.color = '';

                opcion.style.boxShadow = '';

                opcion.style.transform = '';

            }
        );


        /*
        * =========================================
        * ESTADOS DE OPCIONES
        * =========================================
        */

        card.querySelectorAll(
            `
            .correcto,
            .incorrecto,
            .seleccionado,
            .seleccionada,
            .conectado,
            .conectada,
            .usada
            `
        ).forEach(
            element => {

                element.classList.remove(
                    'correcto',
                    'incorrecto',
                    'seleccionado',
                    'seleccionada',
                    'conectado',
                    'conectada',
                    'usada'
                );

            }
        );


        /*
        * =========================================
        * OPCIONES DESHABILITADAS
        * =========================================
        */

        card.querySelectorAll(
            '.opcion-capy'
        ).forEach(
            option => {

                option.disabled = false;

            }
        );


        /*
        * =========================================
        * PALABRAS COLOCADAS
        * =========================================
        */

        card.querySelectorAll(
            '.palabra-colocada'
        ).forEach(
            element => {

                element.remove();

            }
        );


        /*
        * =========================================
        * SUSTANTIVOS DE ARRASTRE
        * =========================================
        */

        card.querySelectorAll(
            '.sustantivo-arrastrable'
        ).forEach(
            elemento => {

                elemento.classList.remove(
                    'usado',
                    'correcto',
                    'incorrecto'
                );

                elemento.hidden = false;

            }
        );


        /*
        * =========================================
        * ZONA DE SUSTANTIVOS
        * =========================================
        */

        const zonaSustantivos =
            card.querySelector(
                '.zona-sustantivos'
            );


        if (zonaSustantivos) {

            zonaSustantivos.classList.remove(
                'completada',
                'arrastre-activo'
            );


            const placeholder =
                zonaSustantivos.querySelector(
                    '.zona-sustantivos-placeholder'
                );


            if (placeholder) {
                placeholder.hidden = false;
            }

        }


        /*
        * =========================================
        * ARTÍCULOS
        * =========================================
        */

        card.querySelectorAll(
            '.espacio-articulo'
        ).forEach(
            space => {

                space.textContent = '?';

                space.removeAttribute(
                    'data-correcto'
                );

                space.removeAttribute(
                    'data-articulo'
                );

                space.classList.remove(
                    'arrastre-activo'
                );

            }
        );


        /*
        * =========================================
        * CLASIFICACIÓN
        * =========================================
        */

        card.querySelectorAll(
            '.palabra-arrastrable'
        ).forEach(
            word => {

                word.removeAttribute(
                    'data-selected'
                );

                word.classList.remove(
                    'seleccionado',
                    'correcto',
                    'incorrecto',
                    'arrastrando'
                );

            }
        );


        /*
        * =========================================
        * CONEXIONES
        * =========================================
        */

        card.querySelectorAll(
            '.elemento-conectar'
        ).forEach(
            element => {

                element.classList.remove(
                    'seleccionado',
                    'conectado',
                    'incorrecto'
                );

            }
        );


        /*
        * =========================================
        * FEEDBACK GENERAL
        * =========================================
        */

        const feedback =
            card.querySelector(
                '.actividad-feedback'
            );


        if (feedback) {

            feedback.hidden = true;

            feedback.textContent = '';

            feedback.className =
                'actividad-feedback';

        }


        /*
        * =========================================
        * RESULTADO DE SUSTANTIVOS
        * =========================================
        */

        const resultadoSustantivos =
            card.querySelector(
                '.resultado-sustantivos'
            );


        if (resultadoSustantivos) {
            resultadoSustantivos.hidden = true;
        }


        /*
        * =========================================
        * SI FUE UN REINICIO MANUAL
        * =========================================
        *
        * También actualizamos inmediatamente
        * el botón Siguiente.
        */

        if (!desdeNavegacion) {
            updateNavigation();
        }

    };
    

    /*
    * =========================================
    * CREAR BOTÓN "INTENTAR DE NUEVO"
    * =========================================
    */

    const agregarBotonReintentar = card => {

        if (!card) {
            return;
        }

        const type =
            normalize(
                card.dataset.tipo
            );

        if (
            type === 'introduccion' ||
            type === 'explicacion'
        ) {
            return;
        }

        if (
            card.querySelector(
                '.btn-reiniciar'
            )
        ) {
            return;
        }

        const explicacion =
            card.querySelector(
                '.actividad-explicacion'
            );


        const boton =
            document.createElement(
                'button'
            );

        boton.type = 'button';

        boton.className =
            'btn-reiniciar';

        boton.innerHTML = `
            <i class="fa-solid fa-rotate-right"></i>
            Intentar de nuevo
        `;


        if (explicacion) {

            explicacion.insertAdjacentElement(
                'afterend',
                boton
            );

        } else {

            const actions =
                card.querySelector(
                    '.actividad-actions'
                );

            if (actions) {

                actions.insertAdjacentElement(
                    'beforebegin',
                    boton
                );

            }

        }

    };


    /*
     * =========================================
     * AUDIO
     * =========================================
     */

    page.querySelectorAll(
        '[data-audio]'
    ).forEach(
        element => {

            element.addEventListener(
                'click',
                event => {

                    event.stopPropagation();

                    playAudio(
                        element.dataset.audio
                    );

                }
            );

        }
    );


    /*
     * =========================================
     * PREPARAR TODAS LAS ACTIVIDADES
     * =========================================
     */

    cards.forEach(card => {

        agregarBotonReintentar(card);
        
        const number =
            Number(
                card.dataset.actividad || 0
            );


        /*
        * Cada tarjeta comienza visualmente limpia.
        * El historial se mantiene en `completed`.
        */

        card.dataset.resuelta = '0';

        card.classList.remove(
            'actividad-completada'
        );


        /*
         * Mezclamos las opciones.
         */
        shuffle(
            card.querySelector(
                '.opciones-container'
            )
        );


        shuffle(
            card.querySelector(
                '.articulos-arrastrables'
            )
        );


        shuffle(
            card.querySelector(
                '.palabras-orden'
            )
        );


        shuffle(
            card.querySelector(
                '.clasificacion-palabras'
            )
        );

        shuffle(
            card.querySelector(
                '.conectar-imagenes'
            )
        );

        shuffle(
            card.querySelector(
                '.conectar-palabras'
            )
        );

        prepararConstruccion(card);

        /*
        * =========================================
        * ACTIVIDAD 4 - LECCIÓN 1
        * RECONOCER SUSTANTIVO
        * =========================================
        */

        if (
            esActividad4Leccion1(card)
        ) {

            /*
            * Esta actividad no necesita
            * una zona para construir.
            */
            const zone =
                card.querySelector(
                    '.zona-oracion'
                );

            if (zone) {
                zone.remove();
            }

            const titulo =
                card.querySelector(
                    'h2'
                );

            if (titulo) {

                titulo.textContent =
                    'Reconoce el sustantivo de animal';

            }

            /*
            * Cambiamos únicamente la instrucción
            * visible de esta actividad.
            */
            const textos =
                card.querySelectorAll(
                    'p, span, div'
                );

            textos.forEach(
                element => {

                    const texto =
                        normalize(
                            element.textContent
                        );

                    if (
                        texto ===
                        'ordena las palabras para formar una oracion con sentido.'
                    ) {

                        element.textContent =
                            'Selecciona la palabra que es un sustantivo.';

                    }

                }
            );
        }

        if (
            esActividad5Leccion1(card)
        ) {

            const zone =
                card.querySelector(
                    '.zona-construccion'
                );

            if (zone) {
                zone.remove();
            }


            const mensaje =
                card.querySelector(
                    '.construir-mensaje'
                );

            if (mensaje) {
                mensaje.remove();
            }

        }

        const type =
            normalize(
                card.dataset.tipo
            );

        if (type === 'arrastre') {
            prepararArrastreSustantivos(card);
        }


        if (
            type === 'introduccion' ||
            type === 'explicacion'
        ) {

            const completeButton =
                card.querySelector(
                    '.btn-completar, .btn-entendido'
                );


            if (completeButton) {

                completeButton.addEventListener(
                    'click',
                    () => {

                        completeActivity(
                            card
                        );


                        showFeedback(
                            card,
                            '¡Muy bien! Ya puedes continuar.',
                            true
                        );

                    }
                );

            }

        }


        /*
         * =========================================
         * HACER OPCIONES ARRASTRABLES
         * =========================================
         */

        card.querySelectorAll(
            '.articulo-arrastrable'
        ).forEach(
            article => {

                article.setAttribute(
                    'draggable',
                    'true'
                );

            }
        );


        card.querySelectorAll(
            '.palabra-arrastrable'
        ).forEach(
            word => {

                word.setAttribute(
                    'draggable',
                    'true'
                );

            }
        );


        /*
         * Elementos de conectar también pueden
         * funcionar mediante clic/tap.
         */
        card.querySelectorAll(
            '.elemento-conectar'
        ).forEach(
            element => {

                element.setAttribute(
                    'tabindex',
                    '0'
                );

            }
        );

    });


    /*
     * =========================================
     * DRAG & DROP - ARTÍCULOS
     * =========================================
     */

    let articuloArrastrado = null;


    page.addEventListener(
        'dragstart',
        event => {

            const article =
                event.target.closest(
                    '.articulo-arrastrable'
                );


            if (!article) {
                return;
            }


            articuloArrastrado =
                article;


            article.classList.add(
                'arrastrando'
            );


            try {

                event.dataTransfer.effectAllowed =
                    'move';

                event.dataTransfer.setData(
                    'text/plain',
                    article.dataset.texto ||
                    article.textContent.trim()
                );

            } catch (error) {}

        }
    );


    page.addEventListener(
        'dragend',
        event => {

            const article =
                event.target.closest(
                    '.articulo-arrastrable'
                );


            if (article) {

                article.classList.remove(
                    'arrastrando'
                );

            }


            articuloArrastrado =
                null;

        }
    );


    page.addEventListener(
        'dragover',
        event => {

            const space =
                event.target.closest(
                    '.espacio-articulo'
                );


            if (
                space &&
                articuloArrastrado
            ) {

                event.preventDefault();

                space.classList.add(
                    'arrastre-activo'
                );

            }

        }
    );


    page.addEventListener(
        'dragleave',
        event => {

            const space =
                event.target.closest(
                    '.espacio-articulo'
                );


            if (space) {

                space.classList.remove(
                    'arrastre-activo'
                );

            }

        }
    );


    page.addEventListener(
        'drop',
        event => {

            const space =
                event.target.closest(
                    '.espacio-articulo'
                );


            if (
                !space ||
                !articuloArrastrado
            ) {

                return;

            }


            event.preventDefault();


            space.classList.remove(
                'arrastre-activo'
            );


            const article =
                articuloArrastrado;


            const card =
                article.closest(
                    '.actividad-card'
                );


            if (
                !card ||
                card.dataset.resuelta === '1'
            ) {

                return;

            }


            if (
                article.dataset.correcta === '1'
            ) {

                space.textContent =
                    article.dataset.texto ||
                    article.textContent.trim();


                space.dataset.correcto =
                    '1';


                space.dataset.articulo =
                    article.dataset.texto ||
                    '';


                article.classList.add(
                    'correcto'
                );


                completeActivity(
                    card
                );


                showFeedback(
                    card,
                    '¡Correcto! Completaste la oración.',
                    true
                );

            } else {

                article.classList.add(
                    'incorrecto'
                );


                showFeedback(
                    card,
                    'Esa palabra no corresponde. Inténtalo de nuevo.',
                    false
                );

            }

        }
    );


    /*
     * =========================================
     * CLIC PRINCIPAL DE LAS DINÁMICAS
     * =========================================
     */

    page.addEventListener(
        'click',
        event => {

            /*
             * =====================================
             * OPCIONES GENERALES / CONSTRUIR
             * =====================================
             */

            const option =
                event.target.closest(
                    '.opcion-capy'
                );


            if (option) {

                const card =
                    option.closest(
                        '.actividad-card'
                    );


                if (
                    !card ||
                    card.dataset.resuelta === '1'
                ) {

                    return;

                }


                const type =
                    normalize(
                        card.dataset.tipo
                    );


                /*
                 * ---------------------------------
                 * CONSTRUIR CON ORDEN
                 * ---------------------------------
                 */

                if (
                    type === 'construir' &&
                    getOrder(option) !== null
                ) {

                    agregarPalabraConstruccion(
                        card,
                        option
                    );

                    return;

                }


                /*
                 * ---------------------------------
                 * CONSTRUIR SIN ORDEN
                 * ---------------------------------
                 *
                 * Varias respuestas correctas.
                 */

                if (
                    type === 'construir'
                ) {

                    const constructionData =
                        prepararConstruccionSeleccion(
                            card
                        );


                    if (
                        constructionData
                    ) {

                        const estabaSeleccionada =
                            option.classList.contains(
                                'seleccionada'
                            );


                        option.classList.toggle(
                            'seleccionada'
                        );


                        /*
                        * ---------------------------------
                        * MARCADO VISUAL
                        * ---------------------------------
                        */

                        if (
                            !estabaSeleccionada
                        ) {

                            if (
                                option.dataset.correcta === '1'
                            ) {

                                option.classList.add(
                                    'correcto'
                                );

                                option.classList.remove(
                                    'incorrecto'
                                );

                                option.style.backgroundColor =
                                    '#d7f5d0';

                                option.style.borderColor =
                                    '#62c554';

                                option.style.color =
                                    '#2f7d2a';

                            } else {

                                option.classList.add(
                                    'incorrecto'
                                );

                                option.classList.remove(
                                    'correcto'
                                );

                                option.style.backgroundColor =
                                    '#ffd9d9';

                                option.style.borderColor =
                                    '#e45b5b';

                                option.style.color =
                                    '#a83232';

                            }

                        } else {

                            option.classList.remove(
                                'correcto',
                                'incorrecto'
                            );

                            option.style.backgroundColor = '';
                            option.style.borderColor = '';
                            option.style.color = '';

                        }


                        /*
                        * ---------------------------------
                        * MENSAJE
                        * ---------------------------------
                        */

                        if (
                            option.classList.contains(
                                'seleccionada'
                            )
                        ) {

                            if (
                                option.dataset.correcta === '1'
                            ) {

                                showFeedback(
                                    card,
                                    '¡Correcto! Esa palabra es un sustantivo.',
                                    true
                                );

                            } else {

                                showFeedback(
                                    card,
                                    'Esa palabra no es un sustantivo.',
                                    false
                                );

                            }

                        }


                        /*
                        * ---------------------------------
                        * COMPROBAR ACTIVIDAD
                        * ---------------------------------
                        */

                        comprobarConstruccionSeleccion(
                            card
                        );


                        return;

                    }

                }


                /*
                 * ---------------------------------
                 * OPCIÓN NORMAL
                 * ---------------------------------
                 */

                if (
                    option.dataset.correcta === '1'
                ) {

                    option.classList.add(
                        'correcto'
                    );


                    card.querySelectorAll(
                        '.opcion-capy'
                    ).forEach(
                        element => {

                            element.disabled =
                                true;

                        }
                    );


                    completeActivity(
                        card
                    );


                    showFeedback(
                        card,
                        '¡Correcto! ¡Excelente trabajo!',
                        true
                    );

                } else {

                    option.classList.add(
                        'incorrecto'
                    );


                    showFeedback(
                        card,
                        'Inténtalo otra vez.',
                        false
                    );

                }


                return;

            }


            /*
             * =====================================
             * ARTÍCULOS POR CLIC
             * =====================================
             */

            const article =
                event.target.closest(
                    '.articulo-arrastrable'
                );


            if (article) {

                const card =
                    article.closest(
                        '.actividad-card'
                    );


                const space =
                    card
                        ? card.querySelector(
                            '.espacio-articulo'
                        )
                        : null;


                if (
                    !card ||
                    !space ||
                    card.dataset.resuelta === '1'
                ) {

                    return;

                }


                if (
                    article.dataset.correcta === '1'
                ) {

                    space.textContent =
                        article.dataset.texto ||
                        article.textContent.trim();


                    space.dataset.correcto =
                        '1';


                    space.dataset.articulo =
                        article.dataset.texto ||
                        '';


                    article.classList.add(
                        'correcto'
                    );


                    completeActivity(
                        card
                    );


                    showFeedback(
                        card,
                        '¡Correcto! Completaste la oración.',
                        true
                    );

                } else {

                    article.classList.add(
                        'incorrecto'
                    );


                    showFeedback(
                        card,
                        'Esa palabra no corresponde. Inténtalo de nuevo.',
                        false
                    );

                }


                return;

            }


            /*
             * =====================================
             * ORDENAR - PALABRAS
             * =====================================
             */

            const word =
                event.target.closest(
                    '.palabra-orden'
                );


            if (word) {

                const card =
                    word.closest(
                        '.actividad-card'
                    );


                if (
                    !card ||
                    card.dataset.resuelta === '1'
                ) {
                    return;
                }


                /*
                * =====================================
                * ORDENAR QUE EN REALIDAD ES SELECCIÓN
                * =====================================
                *
                * Ejemplo:
                * gato      -> correcta
                * correr    -> incorrecta
                * bonito    -> incorrecta
                * rápido    -> incorrecta
                */

                if (
                    esActividadSeleccionOrdenar(card)
                ) {

                    /*
                    * Quitamos cualquier estado anterior.
                    */
                    card.querySelectorAll(
                        '.palabra-orden'
                    ).forEach(
                        opcion => {

                            opcion.classList.remove(
                                'seleccionado'
                            );

                        }
                    );


                    /*
                    * Respuesta correcta.
                    */
                    if (
                        word.dataset.correcta === '1'
                    ) {

                        word.classList.add(
                            'correcto'
                        );


                        completeActivity(
                            card
                        );


                        showFeedback(
                            card,
                            '¡Correcto! Gato es un sustantivo porque nombra a un animal.',
                            true
                        );


                    } else {

                        /*
                        * Respuesta incorrecta.
                        */
                        word.classList.add(
                            'incorrecto'
                        );


                        showFeedback(
                            card,
                            'Esa palabra no es un sustantivo. Inténtalo de nuevo.',
                            false
                        );


                        /*
                        * Quitamos el estado después
                        * de un momento para permitir
                        * volver a intentarlo.
                        */
                        setTimeout(
                            () => {

                                word.classList.remove(
                                    'incorrecto'
                                );

                            },
                            500
                        );

                    }


                    return;
                }


                /*
                * =====================================
                * ORDENAMIENTO REAL
                * =====================================
                *
                * Las actividades que sí tienen
                * orden_correcto continúan usando
                * la lógica original.
                */

                const zone =
                    card.querySelector(
                        '.zona-oracion'
                    );


                if (
                    !zone ||
                    word.classList.contains(
                        'usada'
                    )
                ) {
                    return;
                }


                const order =
                    getOrder(word);


                if (
                    order === null
                ) {
                    return;
                }


                const clone =
                    word.cloneNode(true);


                clone.classList.remove(
                    'usada'
                );


                clone.classList.add(
                    'palabra-colocada'
                );


                clone.dataset.source =
                    word.dataset.opcionId ||
                    word.dataset.id ||
                    '';


                zone.appendChild(
                    clone
                );


                word.classList.add(
                    'usada'
                );


                const selectedWords =
                    [
                        ...zone.querySelectorAll(
                            '.palabra-colocada'
                        )
                    ];


                const expectedWords =
                    [
                        ...card.querySelectorAll(
                            '.palabra-orden'
                        )
                    ]
                    .filter(
                        element =>
                            getOrder(element) !== null
                    )
                    .sort(
                        (a, b) =>
                            getOrder(a) -
                            getOrder(b)
                    );


                if (
                    selectedWords.length !==
                    expectedWords.length
                ) {
                    return;
                }


                const isCorrect =
                    selectedWords.every(
                        (
                            element,
                            index
                        ) => {

                            return (
                                normalize(
                                    element.textContent
                                ) ===
                                normalize(
                                    expectedWords[
                                        index
                                    ].textContent
                                )
                            );

                        }
                    );


                if (isCorrect) {

                    completeActivity(
                        card
                    );


                    showFeedback(
                        card,
                        '¡Orden perfecto! Formaste la oración correctamente.',
                        true
                    );

                } else {

                    showFeedback(
                        card,
                        'Revisa el orden de la oración.',
                        false
                    );

                }


                return;
            }


            /*
             * =====================================
             * PALABRA COLOCADA
             * =====================================
             */

            const placedWord =
                event.target.closest(
                    '.palabra-colocada'
                );


            if (placedWord) {

                const card =
                    placedWord.closest(
                        '.actividad-card'
                    );


                if (
                    !card ||
                    card.dataset.resuelta === '1'
                ) {

                    return;

                }


                const sourceId =
                    placedWord.dataset.source;


                const source =
                    sourceId
                        ? card.querySelector(
                            '[data-opcion-id="' +
                            sourceId +
                            '"]'
                        )
                        : null;


                placedWord.remove();


                if (source) {

                    source.classList.remove(
                        'usada'
                    );

                }


                updateNavigation();

                return;

            }


            /*
            * =====================================
            * CONECTAR PALABRA + IMAGEN
            * =====================================
            */

            const connectElement =
                event.target.closest(
                    '.elemento-conectar'
                );

            if (connectElement) {

                const card =
                    connectElement.closest(
                        '.actividad-card'
                    );

                if (!card) {
                    return;
                }


                /*
                * =========================================
                * NO BLOQUEAMOS LA ACTIVIDAD POR HABERLA
                * COMPLETADO EN EL PASADO.
                *
                * El jugador puede practicarla nuevamente.
                * =========================================
                */

                if (
                    connectElement.classList.contains(
                        'conectado'
                    )
                ) {
                    return;
                }


                /*
                * Buscamos una selección anterior.
                */

                const selected =
                    card.querySelector(
                        '.elemento-conectar.seleccionado'
                    );


                /*
                * Primera selección.
                */

                if (!selected) {

                    connectElement.classList.add(
                        'seleccionado'
                    );

                    return;
                }


                /*
                * Si vuelve a pulsar el mismo elemento,
                * cancelamos la selección.
                */

                if (
                    selected === connectElement
                ) {

                    connectElement.classList.remove(
                        'seleccionado'
                    );

                    return;
                }


                /*
                * =========================================
                * COMPROBAR PAREJA REAL
                * =========================================
                *
                * La pareja se identifica mediante
                * data-pareja, NO mediante grupo.
                */

                const parejaA =
                    String(
                        selected.dataset.pareja || ''
                    );

                const parejaB =
                    String(
                        connectElement.dataset.pareja || ''
                    );


                /*
                * =========================================
                * PAREJA CORRECTA
                * =========================================
                */

                if (
                    parejaA !== '' &&
                    parejaA === parejaB
                ) {

                    selected.classList.remove(
                        'seleccionado'
                    );

                    connectElement.classList.remove(
                        'seleccionado'
                    );


                    selected.classList.add(
                        'conectado'
                    );

                    connectElement.classList.add(
                        'conectado'
                    );


                    /*
                    * Contador de parejas.
                    */

                    const palabras =
                        card.querySelectorAll(
                            '.palabra-conectar'
                        );

                    const conectadas =
                        card.querySelectorAll(
                            '.palabra-conectar.conectado'
                        );


                    const contador =
                        card.querySelector(
                            '.conexiones-numero'
                        );

                    if (contador) {

                        contador.textContent =
                            conectadas.length;

                    }


                    /*
                    * =====================================
                    * ¿TERMINÓ TODAS LAS PAREJAS?
                    * =====================================
                    */

                    if (
                        palabras.length > 0 &&
                        conectadas.length === palabras.length
                    ) {

                        /*
                        * Marcar la actividad como completada.
                        */

                        card.dataset.resuelta = '1';

                        completeActivity(
                            card
                        );

                        showFeedback(
                            card,
                            '¡Excelente! Relacionaste todas las palabras con su imagen.',
                            true
                        );

                    } else {

                        showFeedback(
                            card,
                            '¡Pareja correcta! Sigue con las demás.',
                            true
                        );

                    }


                    updateNavigation();

                    return;
                }


                /*
                * =========================================
                * PAREJA INCORRECTA
                * =========================================
                */

                selected.classList.remove(
                    'seleccionado'
                );

                selected.classList.add(
                    'incorrecto'
                );

                connectElement.classList.add(
                    'incorrecto'
                );


                showFeedback(
                    card,
                    'Esa no es la imagen que corresponde. Inténtalo de nuevo.',
                    false
                );


                setTimeout(
                    () => {

                        selected.classList.remove(
                            'incorrecto'
                        );

                        connectElement.classList.remove(
                            'incorrecto'
                        );

                    },
                    500
                );


                return;
            }


            /*
             * =====================================
             * CLASIFICACIÓN
             * =====================================
             */

            const classificationWord =
                event.target.closest(
                    '.palabra-arrastrable'
                );


            const classificationGroup =
                event.target.closest(
                    '.grupo-destino'
                );


            if (
                classificationWord &&
                !classificationWord.dataset.selected
            ) {

                const card =
                    classificationWord.closest(
                        '.actividad-card'
                    );


                if (
                    !card ||
                    card.dataset.resuelta === '1'
                ) {

                    return;

                }


                card.querySelectorAll(
                    '.palabra-arrastrable'
                ).forEach(
                    element => {

                        element.removeAttribute(
                            'data-selected'
                        );

                        element.classList.remove(
                            'seleccionado'
                        );

                    }
                );


                classificationWord.dataset.selected =
                    '1';


                classificationWord.classList.add(
                    'seleccionado'
                );


                return;

            }


            if (classificationGroup) {

                const card =
                    classificationGroup.closest(
                        '.actividad-card'
                    );


                if (
                    !card ||
                    card.dataset.resuelta === '1'
                ) {

                    return;

                }


                const selectedWord =
                    card.querySelector(
                        '.palabra-arrastrable[data-selected="1"]'
                    );


                if (!selectedWord) {

                    showFeedback(
                        card,
                        'Primero selecciona una palabra.',
                        false
                    );

                    return;

                }


                const correctGroup =
                    normalize(
                        selectedWord.dataset.grupo
                    );


                const targetGroup =
                    normalize(
                        classificationGroup.dataset.grupo
                    );


                if (
                    correctGroup ===
                    targetGroup
                ) {

                    classificationGroup.appendChild(
                        selectedWord
                    );


                    selectedWord.removeAttribute(
                        'data-selected'
                    );


                    selectedWord.classList.remove(
                        'seleccionado'
                    );


                    selectedWord.classList.add(
                        'correcto'
                    );


                    const remaining =
                        card.querySelectorAll(
                            '.palabra-arrastrable:not(.correcto)'
                        );


                    if (
                        remaining.length === 0
                    ) {

                        completeActivity(
                            card
                        );


                        showFeedback(
                            card,
                            '¡Clasificaste todas las palabras correctamente!',
                            true
                        );

                    }

                } else {

                    showFeedback(
                        card,
                        'Prueba con otro grupo.',
                        false
                    );

                }

            }

        }
    );


    /*
     * =========================================
     * CLIC DE BOTONES
     * =========================================
     */

    page.addEventListener(
        'click',
        event => {

            /*
             * ---------------------------------
             * REINICIAR
             * ---------------------------------
             */

            const resetButton =
                event.target.closest(
                    '.btn-reiniciar'
                );

            if (resetButton) {

                const card =
                    resetButton.closest(
                        '.actividad-card'
                    );

                if (!card) {
                    return;
                }

                resetCard(card);

                const type =
                    normalize(
                        card.dataset.tipo
                    );


                if (
                    type === 'conectar' ||
                    type === 'arrastre'
                ) {

                    card.dataset.resuelta =
                        '0';

                }


                updateNavigation();

                return;
            }


            /*
             * ---------------------------------
             * FINALIZAR
             * ---------------------------------
             */

            const finalButton =
                event.target.closest(
                    '.btn-finalizar'
                );


            if (finalButton) {

                event.preventDefault();


                const card =
                    getCurrentCard();


                if (
                    !card ||
                    !isActivityComplete(card)
                ) {

                    showFeedback(
                        card,
                        'Completa esta actividad antes de finalizar la lección.',
                        false
                    );

                    return;

                }


                completeActivity(
                    card
                );


                saveProgress()
                    .finally(
                        () => {

                            window.location.href =
                                finalButton.href;

                        }
                    );


                return;

            }


            /*
             * ---------------------------------
             * SIGUIENTE
             * ---------------------------------
             */

            const nextButton =
                event.target.closest(
                    '.btn-siguiente'
                );


            if (nextButton) {

                event.preventDefault();


                const card =
                    getCurrentCard();


                if (
                    !card ||
                    !isActivityComplete(card)
                ) {

                    showFeedback(
                        card,
                        'Completa esta actividad antes de continuar.',
                        false
                    );

                    return;

                }


                if (
                    current < total
                ) {

                    showActivity(
                        current + 1
                    );

                }


                return;

            }


            /*
             * ---------------------------------
             * ANTERIOR
             * ---------------------------------
             */

            const previousButton =
                event.target.closest(
                    '.btn-anterior'
                );


            if (previousButton) {

                event.preventDefault();


                if (
                    current > 1
                ) {

                    showActivity(
                        current - 1
                    );

                }

            }

        }
    );


    /*
     * =========================================
     * DRAG & DROP - CLASIFICACIÓN
     * =========================================
     */

    let palabraClasificacionArrastrada = null;


    page.addEventListener(
        'dragstart',
        event => {

            const word =
                event.target.closest(
                    '.palabra-arrastrable'
                );


            if (!word) {
                return;
            }


            palabraClasificacionArrastrada =
                word;


            word.classList.add(
                'arrastrando'
            );


            try {

                event.dataTransfer.effectAllowed =
                    'move';

                event.dataTransfer.setData(
                    'text/plain',
                    word.textContent.trim()
                );

            } catch (error) {}

        }
    );


    page.addEventListener(
        'dragend',
        event => {

            const word =
                event.target.closest(
                    '.palabra-arrastrable'
                );


            if (word) {

                word.classList.remove(
                    'arrastrando'
                );

            }


            palabraClasificacionArrastrada =
                null;

        }
    );


    page.addEventListener(
        'dragover',
        event => {

            const group =
                event.target.closest(
                    '.grupo-destino'
                );


            if (
                group &&
                palabraClasificacionArrastrada
            ) {

                event.preventDefault();

                group.classList.add(
                    'arrastre-activo'
                );

            }

        }
    );


    page.addEventListener(
        'dragleave',
        event => {

            const group =
                event.target.closest(
                    '.grupo-destino'
                );


            if (group) {

                group.classList.remove(
                    'arrastre-activo'
                );

            }

        }
    );


    page.addEventListener(
        'drop',
        event => {

            const group =
                event.target.closest(
                    '.grupo-destino'
                );


            if (
                !group ||
                !palabraClasificacionArrastrada
            ) {

                return;

            }


            event.preventDefault();


            group.classList.remove(
                'arrastre-activo'
            );


            const word =
                palabraClasificacionArrastrada;


            const card =
                word.closest(
                    '.actividad-card'
                );


            if (
                !card ||
                card.dataset.resuelta === '1'
            ) {

                return;

            }


            const correctGroup =
                normalize(
                    word.dataset.grupo
                );


            const targetGroup =
                normalize(
                    group.dataset.grupo
                );


            if (
                correctGroup ===
                targetGroup
            ) {

                group.appendChild(
                    word
                );


                word.classList.remove(
                    'seleccionado',
                    'incorrecto'
                );


                word.classList.add(
                    'correcto'
                );


                const remaining =
                    card.querySelectorAll(
                        '.palabra-arrastrable:not(.correcto)'
                    );


                if (
                    remaining.length === 0
                ) {

                    completeActivity(
                        card
                    );


                    showFeedback(
                        card,
                        '¡Clasificaste todas las palabras correctamente!',
                        true
                    );

                } else {

                    showFeedback(
                        card,
                        '¡Correcto! Sigue clasificando.',
                        true
                    );

                }

            } else {

                word.classList.add(
                    'incorrecto'
                );


                setTimeout(
                    () => {

                        word.classList.remove(
                            'incorrecto'
                        );

                    },
                    500
                );


                showFeedback(
                    card,
                    'Ese no es el grupo correcto. Inténtalo de nuevo.',
                    false
                );

            }

        }
    );


    /*
     * =========================================
     * DRAG & DROP - ORDENAR
     * =========================================
     */

    let palabraOrdenArrastrada = null;


    page.addEventListener(
        'dragstart',
        event => {

            const word =
                event.target.closest(
                    '.palabra-orden'
                );


            if (!word) {
                return;
            }


            /*
             * Solo hacemos drag real
             * cuando existe orden.
             */
            if (
                getOrder(word) === null
            ) {

                return;

            }


            palabraOrdenArrastrada =
                word;


            word.classList.add(
                'arrastrando'
            );


            try {

                event.dataTransfer.effectAllowed =
                    'move';

                event.dataTransfer.setData(
                    'text/plain',
                    word.textContent.trim()
                );

            } catch (error) {}

        }
    );


    page.addEventListener(
        'dragend',
        event => {

            const word =
                event.target.closest(
                    '.palabra-orden'
                );


            if (word) {

                word.classList.remove(
                    'arrastrando'
                );

            }


            palabraOrdenArrastrada =
                null;

        }
    );


    /*
     * =========================================
     * MOSTRAR ACTIVIDAD INICIAL
     * =========================================
     */

    showActivity(
        current
    );

});