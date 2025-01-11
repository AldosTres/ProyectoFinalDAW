import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData, renderPagination } from "./admin_page_utils.js"
$(document).ready(function () {
    /**
     * Array de valores para la seccion sobre nosotros
     */
    const jumpstyleValues = [
        {
            "title": "Pasión por el Jumpstyle",
            "description": "Celebramos y promovemos el Jumpstyle como una forma de expresión artística que conecta a personas de todo el mundo."
        },
        {
            "title": "Unión y Comunidad",
            "description": "Creemos en el poder del Jumpstyle para derribar barreras culturales y unir a bailarines de diferentes lugares del mundo."
        },
        {
            "title": "Creatividad y Autenticidad",
            "description": "Fomentamos la innovación en el baile, apoyando a todos los que buscan encontrar su estilo único."
        },
        {
            "title": "Disciplina y Superación",
            "description": "Inspiramos a los bailarines a trabajar duro y desafiarse a sí mismos, alcanzando nuevas metas en cada competición."
        },
        {
            "title": "Diversión y Energía",
            "description": "Celebramos la energía vibrante del Jumpstyle, recordando que bailar es una experiencia que debe disfrutarse."
        },
        {
            "title": "Respeto Mutuo",
            "description": "Valoramos el respeto entre los participantes, creando un ambiente positivo en cada torneo."
        },
        {
            "title": "Longevidad del Jumpstyle",
            "description": "Trabajamos para preservar y expandir la esencia del Jumpstyle, asegurando que continúe inspirando a futuras generaciones."
        }
    ]

    /**
     * Array de propietarios para la seccion sobre nosotros
     */

    const jlsOwners = [
        {
            'name': 'Daiser',
            'url_image':'daiser.jpeg'
        },
        {
            'name': 'Ivi',
            'url_image':'ivi.jpg'
        },
        {
            'name': 'Dejux',
            'url_image':'djuana.jpg'
        }
    ]
    
    /**
     * Función que genera los valores de la empresa en la página
     */
    function generateValues() {
        let values = ``
        jumpstyleValues.forEach(value => {
            values += `<div class="about-us__value-card">
                                <p class="about-us__value-title">${value['title']}</p>
                                <p class="about-us__value-description">${value['description']}</p>
                              </div>`
        })
        $('.values__list').html(values)

    }
    generateValues()

     /**
     * Función que genera el contenido de los propietarios en la seccion sobre nosotros
     */
    function generateOwners() {
        let owners = ``
        jlsOwners.forEach(owner => {
            owners += `<div class="team__owner-card">
                            <img src="${BASE_URL}img/propietarios/${owner['url_image']}" alt="">
                            <div class="team__owner-card-content">
                                <p class="team__owner-card-title">${owner['name']}</p>
                            </div>
                        </div>`
        })

        $('.team__owners-container').html(owners)
    }
    generateOwners()
});