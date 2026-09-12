
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
            parseInt(page.dataset.actividadInicial || '1', 10) || 1
        )
    );

    let points = parseInt(page.dataset.puntosIniciales || '0', 10) || 0;
    const completed = new Set();
    let audioActual = null;

    const normalize = value => {
        return String(value || '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    };

    const shuffle = container => {
        if (!container) {
            return;
        }

        const items = [...container.children];

        for (let i = items.length - 1; i > 0; i--) {
            const randomIndex = Math.floor(Math.random() * (i + 1));

            [items[i], items[randomIndex]] = [
                items[randomIndex],
                items[i]
            ];
        }

        items.forEach(item => {
            container.appendChild(item);
        });
    };

    const showFeedback = (card, message, correct = true) => {
        const feedback = card.querySelector('.actividad-feedback');

        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.hidden = false;
        feedback.className =
            'actividad-feedback ' +
            (correct ? 'correcto' : 'incorrecto');
    };

    const saveProgress = () => {
        const data = {
            leccion_id: page.dataset.leccionId,
            actividad_actual: current,
            porcentaje: Math.round(
                completed.size / Math.max(total, 1) * 100
            ),
            puntos: points,
            completada: completed.size === total ? 1 : 0
        };

        fetch('php/guardar-progreso-capy.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).catch(() => {});
    };

    const showActivity = number => {
        current = Math.max(1, Math.min(total, number));

        cards.forEach((card, index) => {
            const cardNumber = index + 1;
            const isCurrent = cardNumber === current;

            card.hidden = !isCurrent;
            card.classList.toggle('activa', isCurrent);
            card.setAttribute('aria-hidden', String(!isCurrent));
        });

        if (progress) {
            progress.style.width =
                (current / total * 100) + '%';
        }

        if (currentLabel) {
            currentLabel.textContent = current;
        }

        saveProgress();
    };

    const completeActivity = card => {
        const number = Number(card.dataset.actividad || 0);

        if (!completed.has(number)) {
            completed.add(number);
            points += Number(card.dataset.puntos || 0);
        }

        card.dataset.resuelta = '1';
        card.classList.add('actividad-completada');

        const nextButton = card.querySelector('.btn-siguiente');

        if (nextButton) {
            nextButton.disabled = false;
        }

        saveProgress();
    };

    const playAudio = url => {
        if (!url) {
            return;
        }

        try {
            if (audioActual) {
                audioActual.pause();
            }

            audioActual = new Audio(url);
            audioActual.play().catch(() => {});
        } catch (error) {}
    };

    const resetCard = card => {
        card.dataset.resuelta = '0';
        card.classList.remove('actividad-completada');

        card.querySelectorAll(
            '.correcto, .incorrecto, .seleccionado, .seleccionada, .conectado, .conectada, .usada, .colocada'
        ).forEach(element => {
            element.classList.remove(
                'correcto',
                'incorrecto',
                'seleccionado',
                'seleccionada',
                'conectado',
                'conectada',
                'usada',
                'colocada'
            );
        });

        card.querySelectorAll('.palabra-colocada').forEach(element => {
            element.remove();
        });

        card.querySelectorAll('[data-selected]').forEach(element => {
            element.removeAttribute('data-selected');
        });

        card.querySelectorAll('.espacio-articulo').forEach(space => {
            space.textContent = '?';
            space.removeAttribute('data-correcto');
            space.removeAttribute('data-articulo');
        });

        const feedback = card.querySelector('.actividad-feedback');

        if (feedback) {
            feedback.hidden = true;
            feedback.textContent = '';
            feedback.className = 'actividad-feedback';
        }
    };

    page.querySelectorAll('[data-audio]').forEach(element => {
        element.addEventListener('click', event => {
            event.stopPropagation();
            playAudio(element.dataset.audio);
        });
    });

    cards.forEach(card => {
        shuffle(card.querySelector('.opciones-container'));
        shuffle(card.querySelector('.articulos-arrastrables'));
        shuffle(card.querySelector('.palabras-orden'));
        shuffle(card.querySelector('.clasificacion-palabras'));

        const type = normalize(card.dataset.tipo);

        if (
            type === 'introduccion' ||
            type === 'explicacion' ||
            type === 'identificacion'
        ) {
            const completeButton = card.querySelector(
                '.btn-completar, .btn-entendido'
            );

            if (completeButton) {
                completeButton.addEventListener('click', () => {
                    completeActivity(card);

                    showFeedback(
                        card,
                        '¡Muy bien! Ya puedes continuar.',
                        true
                    );
                });
            }
        }
    });

    page.addEventListener('click', event => {
        const option = event.target.closest('.opcion-capy');

        if (option) {
            const card = option.closest('.actividad-card');

            if (!card || card.dataset.resuelta === '1') {
                return;
            }

            if (option.dataset.correcta === '1') {
                option.classList.add('correcto');

                card.querySelectorAll('.opcion-capy').forEach(element => {
                    element.disabled = true;
                });

                completeActivity(card);

                showFeedback(
                    card,
                    '¡Correcto! ¡Excelente trabajo!',
                    true
                );
            } else {
                option.classList.add('incorrecto');

                showFeedback(
                    card,
                    'Inténtalo otra vez.',
                    false
                );
            }

            return;
        }

        const article = event.target.closest('.articulo-arrastrable');

        if (article) {
            const card = article.closest('.actividad-card');
            const space = card
                ? card.querySelector('.espacio-articulo')
                : null;

            if (
                !card ||
                !space ||
                card.dataset.resuelta === '1'
            ) {
                return;
            }

            if (article.dataset.correcta === '1') {
                space.textContent =
                    article.dataset.texto ||
                    article.textContent.trim();

                space.dataset.correcto = '1';
                space.dataset.articulo =
                    article.dataset.texto || '';

                article.classList.add('correcto');

                completeActivity(card);

                showFeedback(
                    card,
                    '¡Correcto! Completaste la oración.',
                    true
                );
            } else {
                article.classList.add('incorrecto');

                showFeedback(
                    card,
                    'Esa palabra no corresponde. Inténtalo de nuevo.',
                    false
                );
            }

            return;
        }

        const word = event.target.closest('.palabra-orden');

        if (word) {
            const card = word.closest('.actividad-card');
            const zone = card
                ? card.querySelector('.zona-oracion')
                : null;

            if (
                !card ||
                !zone ||
                card.dataset.resuelta === '1' ||
                word.classList.contains('usada')
            ) {
                return;
            }

            const clone = word.cloneNode(true);

            clone.classList.remove('usada');
            clone.classList.add('palabra-colocada');
            clone.dataset.source =
                word.dataset.opcionId || '';

            zone.appendChild(clone);
            word.classList.add('usada');

            const selectedWords = [
                ...zone.querySelectorAll('.palabra-colocada')
            ];

            const expectedWords = [
                ...card.querySelectorAll('.palabra-orden')
            ]
                .filter(element => element.dataset.orden)
                .sort((a, b) => {
                    return Number(a.dataset.orden) -
                        Number(b.dataset.orden);
                });

            if (selectedWords.length === expectedWords.length) {
                const isCorrect = selectedWords.every((element, index) => {
                    return normalize(element.textContent) ===
                        normalize(expectedWords[index].textContent);
                });

                if (isCorrect) {
                    completeActivity(card);

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
            }

            return;
        }

        const placedWord = event.target.closest('.palabra-colocada');

        if (placedWord) {
            const card = placedWord.closest('.actividad-card');

            if (!card || card.dataset.resuelta === '1') {
                return;
            }

            const sourceId = placedWord.dataset.source;

            const source = card.querySelector(
                '[data-opcion-id="' + sourceId + '"]'
            );

            placedWord.remove();

            if (source) {
                source.classList.remove('usada');
            }

            return;
        }

        const connectElement = event.target.closest(
            '.elemento-conectar'
        );

        if (connectElement) {
            const card = connectElement.closest('.actividad-card');

            if (!card || card.dataset.resuelta === '1') {
                return;
            }

            const selected = card.querySelector(
                '.elemento-conectar.seleccionado'
            );

            if (!selected) {
                connectElement.classList.add('seleccionado');
                return;
            }

            if (selected === connectElement) {
                connectElement.classList.remove('seleccionado');
                return;
            }

            if (
                selected.dataset.grupo &&
                selected.dataset.grupo === connectElement.dataset.grupo
            ) {
                selected.classList.remove('seleccionado');
                selected.classList.add('conectado');

                connectElement.classList.add('conectado');

                const connected = card.querySelectorAll(
                    '.elemento-conectar.conectado'
                ).length;

                const totalElements = card.querySelectorAll(
                    '.elemento-conectar'
                ).length;

                if (connected === totalElements) {
                    completeActivity(card);

                    showFeedback(
                        card,
                        '¡Todas las parejas están correctas!',
                        true
                    );
                }
            } else {
                selected.classList.remove('seleccionado');

                showFeedback(
                    card,
                    'Esas parejas no coinciden. Inténtalo de nuevo.',
                    false
                );
            }

            return;
        }

        const classificationWord = event.target.closest(
            '.palabra-arrastrable'
        );

        const classificationGroup = event.target.closest(
            '.grupo-destino'
        );

        if (
            classificationWord &&
            !classificationWord.dataset.selected
        ) {
            const card = classificationWord.closest('.actividad-card');

            if (!card || card.dataset.resuelta === '1') {
                return;
            }

            card.querySelectorAll('.palabra-arrastrable').forEach(element => {
                element.removeAttribute('data-selected');
                element.classList.remove('seleccionado');
            });

            classificationWord.dataset.selected = '1';
            classificationWord.classList.add('seleccionado');

            return;
        }

        if (classificationGroup) {
            const card = classificationGroup.closest('.actividad-card');

            if (!card || card.dataset.resuelta === '1') {
                return;
            }

            const selectedWord = card.querySelector(
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

            const correctGroup = normalize(
                selectedWord.dataset.grupo
            );

            const targetGroup = normalize(
                classificationGroup.dataset.grupo
            );

            if (correctGroup === targetGroup) {
                classificationGroup.appendChild(selectedWord);

                selectedWord.removeAttribute('data-selected');
                selectedWord.classList.remove('seleccionado');
                selectedWord.classList.add('correcto');

                const remaining = card.querySelectorAll(
                    '.palabra-arrastrable:not(.correcto)'
                );

                if (remaining.length === 0) {
                    completeActivity(card);

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
    });

    page.addEventListener('click', event => {
        const resetButton = event.target.closest(
            '.btn-reiniciar-arrastre, .btn-reiniciar'
        );

        if (resetButton) {
            const card = resetButton.closest('.actividad-card');

            if (card) {
                resetCard(card);
            }

            return;
        }

        const nextButton = event.target.closest('.btn-siguiente');
        const previousButton = event.target.closest('.btn-anterior');

        if (nextButton) {
            event.preventDefault();

            if (current < total) {
                showActivity(current + 1);
            }

            return;
        }

        if (previousButton) {
            event.preventDefault();

            if (current > 1) {
                showActivity(current - 1);
            }
        }
    });

    showActivity(current);
});