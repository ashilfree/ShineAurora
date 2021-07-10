
/**
 * @property {HTMLElement} price
 * @property {HTMLElement} select
 */

export default class Filter {

    /**
     *
     * @param {HTMLElement|null} element
     */
    constructor(element) {
        if (element === null) {
            return
        }
        this.price = document.querySelector('#slider-range-update');

        this.bindEvents();
    }


    bindEvents() {
        // this.price.addEventListener('click', e => {
        //     e.preventDefault()
        //     console.log($('#amount').data('max'));
        //     // this.loadUrl(e.target.getAttribute('href'))
        // });
    }

    async loadForm() {
        this.page = 1;
        const data = new FormData(this.form);
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        const params = new URLSearchParams();
        data.forEach((value, key) => {
            params.append(key, value.toString());
        });
        return this.loadUrl(url.pathname + '?' + params.toString());
    }



    loadUrl(url, append = false) {

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    flipContent(content, append) {
        const springConfig = 'gentle';
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({translateY, opacity}) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            })
        };
        const appearSpring = function (element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({translateY, opacity}) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 20
            })
        };

        const flipper = new Flipper({
            element: this.content
        });
        // this.content.children.forEach(element => {
        //     flipper.addFlipped({
        //         element,
        //         spring: springConfig,
        //         flipId: element.id,
        //         shouldFlip: false,
        //         onExit: exitSpring
        //     })
        // });
        // flipper.recordBeforeUpdate();
        if (append) {
            this.content.innerHTML += content;
        } else {
            this.content.innerHTML = content;
        }
        $('.isotope-grid').isotope('reloadItems').isotope();
        // this.content.children.forEach(element => {
        //     flipper.addFlipped({
        //         element,
        //         spring: springConfig,
        //         flipId: element.id,
        //         onAppear: appearSpring
        //     })
        // });
        //
        // flipper.update();
        this.reinitializeModal();
     //   this.reinitializeIsotope();
    }

    updatePrices({min, max}) {
        const slider = document.getElementById('slider');
        if (slider === null) {
            return
        }
        slider.noUiSlider.updateOptions({
           // start: [parseInt(min), parseInt(max)],
            range: {
                min: [parseInt(min)],
                max: [parseInt(max)]
            }
        })
    }

    reinitializeIsotope() {
        $('.isotope-grid').isotope({
            itemSelector: '.isotope-item',
            layoutMode: 'fitRows',
            percentPosition: true,
            animationEngine : 'best-available',
            masonry: {
                columnWidth: '.isotope-item'
            }
        }).isotope();
    }
}