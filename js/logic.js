/** 
 * @since PM (27.11.2020) 
 * @since PM (13.03.2022) the dependency to jquery has been removed
 * @copyright taPMeppe solutions
 * 
 * const TAPMORPH = {...}, is set in .twig
 */
const
	// $ = jQuery.noConflict(), //@see https://api.jquery.com/jQuery.noConflict/
	TAPMORPH_SCROLLER = document.querySelector('html, body'),
	// TAPMORPH_SCROLLER = $('html, body'),
	TAPMORPH_ACTIVE = 'active',
	TAPMORPH_CONTENT = 'content',
	TAPMORPH_NOTIFICATION = '.notification',
	TAPMORPH_TRAPEZE = '.trapeze'
	;

/**
 * @description this function is used to generate the authentication code
 * @param {string} key the location key
 * @returns {string} the authentication code
 */
const TAPMORPH_AUTH = key => {
	const regex = /\W+/g
	return btoa(btoa(location[key].replace(regex, '#')).replace(regex, '@')).replace(regex, '')
}

/**
 * @description this function is used to send a POST request to the server & handle its response
 * @since PM (01.03.2021) standalone
 * @param {{}} data the data to send to the server
 * @param {Function} response the callback used to handle the server response
 * @param {boolean} response.success TRUE if everything went without an exception
 * @param {boolean} response.success2 TRUE if the algorithm's response was 'success'
 * @param {string} response.text the response text from the server
 */
const TAPMORPH_POST = (data, response) => {
	const data2 = new FormData(), http = new XMLHttpRequest();

	data2.append(TAPMORPH.auth, TAPMORPH_AUTH('href'))
	Object.entries(data).forEach(([key, value]) => data2.append(key, value)) //@see https://reactgo.com/javascript-loop-through-object/#objectentries

	http.open("POST", '', true)
	http.setRequestHeader('auth-code', TAPMORPH_AUTH('origin'))
	http.onreadystatechange = function () {
		if (this.readyState == XMLHttpRequest.DONE) { //TRUE once the client receives the server response
			if (TAPMORPH.dev) console.log(this.status, this.responseText)
			response(this.status == 200, this.responseText == 'success', this.responseText)
		}
	}
	http.send(data2)
}

/**
 * @description this function is used to set the registration handlers
 * @requires TAPMORPH_POST
 * @param {Function} response the callback run after the server response
 * @param {HTMLElement} response.form the registration form element
 */
const TAPMORPH_REGISTRATION = response => {
	const form = document.getElementById('registration')
	if (form) { //TRUE if the registration snippet is available
		const
			button = form.querySelector('button'),
			address = form.querySelector('input.email'),
			privacy = form.querySelector('input.privacy-policy'),
			notif = '#registration ~ ' + TAPMORPH_NOTIFICATION
			;
		button.disabled = true //security measure

		address.addEventListener(
			'focus',
			_ => document.querySelectorAll(notif + '.' + TAPMORPH_ACTIVE).forEach(elt => elt.classList.remove(TAPMORPH_ACTIVE))
		)
		privacy.addEventListener('click', _ => button.disabled = !privacy.checked)
		button.addEventListener(
			'click',
			function () { //submit handler
				const email = address.value.trim()
				if (email.match(/^[\w.%+-]+@[\w.-]+\.[A-Za-z]{2,}$/)) {
					//disable the form elements
					const
						button = this, //required to enable access to the element in the anonymous functions used below
						progress = document.querySelector(notif + '-progress').classList
						;
					button.disabled = true
					address.disabled = true
					progress.add(TAPMORPH_ACTIVE)

					TAPMORPH_POST(
						{
							[TAPMORPH.name]: form.querySelector('input.name').value,
							[TAPMORPH.email]: email
						},
						(success, success2, text) => {
							progress.remove(TAPMORPH_ACTIVE)
							//...
							if (success) {
								if (success2) address.value = '';
								document.querySelector(notif + '-' + text).classList.add(TAPMORPH_ACTIVE)
							} else document.querySelector(notif + '-error').classList.add(TAPMORPH_ACTIVE)
							response(form)

							//re-enable the form elements
							button.disabled = false
							address.disabled = false
						}
					)
				} else address.focus()
			}
		)
	}
}


/**
 * @since PM (20.01.2020) the registration form handlers
 * @since PM (27.11.2020) - activate elements on start;
 * 												- this block is used to set the global event handlers
 * @since PM (29.11.2020) remove unwanted elements
 * @since PM (xx.12.2020) handle the empty content's footer
 * @since PM (11.03.2021) the template form submission handler
 */
(_ => {
	/**
	 * @since PM (07.03.2021)
	 * @see https://www.w3schools.com/js/js_cookies.asp
	 * @see https://getbootstrap.com/docs/5.1/components/modal/#show
	 */
	const cookies = document.getElementById(TAPMORPH.cookies)
	if (cookies) {
		new bootstrap.Modal(cookies).show()
		// $(cookies).modal('show')
		cookies.addEventListener(
			'click',
			event => {
				const elt = event.target
				if (elt.matches('button.submit')) {
					if (elt.matches('button.submit-all')) cookies.querySelectorAll('input[type=checkbox][name]').forEach(elt => elt.checked = true)
					cookies.querySelector(`input[type=hidden][name=${TAPMORPH.auth}]`).value = TAPMORPH_AUTH('href')
					cookies.querySelector('form').submit()
				}
			}
		)
	}

	//activate elements on start
	const attr = `[data-${TAPMORPH_ACTIVE}]`
	document.querySelectorAll(attr).forEach(elt => {
		elt.classList.add(TAPMORPH_ACTIVE)
		elt.removeAttribute(attr)
	})

	//remove unwanted elements on the productive version
	if (!TAPMORPH.dev) document.querySelectorAll(`#${TAPMORPH_CONTENT} > :not(${TAPMORPH_TRAPEZE})`).forEach(elt => elt.remove())

	/**
	 * handle the empty content's footer
	 * @alternative document.getElementById(TAPMORPH_CONTENT).childElementCount
	 */
	if (!document.querySelector(`#${TAPMORPH_CONTENT} > ${TAPMORPH_TRAPEZE}`)) document.getElementById('footer').classList.add('bottom')

	/**
	 * listen on scroll
	 * @see https://www.geeksforgeeks.org/how-to-get-the-position-of-scrollbar-using-javascript/
	 * @see https://www.w3schools.com/jsref/prop_element_offsettop.asp
	 * 
	 * TODO replace the current logic by the native intersection observer
	 */
	let scrollY = NaN //the previous scrollbar vertical position
	const
		content = document.getElementById(TAPMORPH_CONTENT),
		offset = (() => {
			const trapeze = document.querySelector(`#${TAPMORPH_CONTENT} > ${TAPMORPH_TRAPEZE}`)
			return trapeze ? parseInt((window.getComputedStyle(trapeze) || trapeze.currentStyle).marginTop) : 100 //in px
		})(),
		top = (offset => content ? content.offsetTop - offset : 0)(offset),
		scrollHandlerInner = (elt, limit) => {
			if (elt) {
				const y = window.scrollY, classList = elt.classList
				if (
					(isNaN(scrollY) || y > scrollY) && //TRUE if scrolling down
					y > limit &&
					!classList.contains(TAPMORPH_ACTIVE)
				) classList.add(TAPMORPH_ACTIVE)
				else if (
					(isNaN(scrollY) || y < scrollY) && //TRUE if scrolling up
					y < limit &&
					classList.contains(TAPMORPH_ACTIVE)
				) classList.remove(TAPMORPH_ACTIVE)
			}
		},
		scrollHandler = () => {
			scrollHandlerInner(document.getElementById('navigation'), top)
			scrollHandlerInner(document.getElementById('topper'), offset)
			//DO NOT SWITCH
			scrollY = window.scrollY
		}
	window.addEventListener('scroll', scrollHandler)
	window.addEventListener('resize', scrollHandler) //trigger the scroll event handler on resize
	document.addEventListener('DOMContentLoaded', scrollHandler) //trigger the scroll event on loaded
	// alternative @see https://www.tutorialspoint.com/What-is-document-ready-equivalent-in-JavaScript
	// $(document).on('ready', scrollHandler) //trigger the scroll event on loaded

	/**
	 * languages activator
	 * this had to be done because of a new error 'popper.js missing' even though popper.js has been added
	 * @see https://getbootstrap.com/docs/4.5/components/dropdowns/#via-javascript
	 */
	const languages = document.getElementById('button_languages')
	if (languages) languages.addEventListener('click', () => {
		languages.classList.toggle('show')
		document.getElementById('container_languages').classList.toggle('show')
	})
	// const languages = $('#button_languages').on('click', () => {
	// 	languages.toggleClass('show')
	// 	$('#container_languages').toggleClass('show')
	// })

	/**
	 * @since PM (20.01.2020) activation of targeted nodes
	 * @since PM (27.11.2020) event handlers related to the scrolling process
	 * @see https://caniuse.com/css-scroll-behavior
	 */
	const
		scrollToAnimate = top => {
			let duration = 1000; //[ms]
			const
				pulse = 100, //[ms]
				step = (top - TAPMORPH_SCROLLER.scrollTop) / (duration / pulse),
				timer = setInterval(
					() => {
						try {
							if (duration <= 0) {
								clearInterval(timer)
								TAPMORPH_SCROLLER.scrollTop = top
							} else {
								duration -= pulse
								TAPMORPH_SCROLLER.scrollTop -= step
							}
						} catch (error) {
							console.error(error)
							clearInterval(timer)
						}
					},
					pulse
				)
		},
		scrollTo = elt => {
			// the safari check has been removed since its new version now supports: css-scroll-behavior
			// @see https://caniuse.com/css-scroll-behavior
			// if (TAPMORPH.browser == 'safari') scrollToAnimate(elt.offsetTop)
			// // if (TAPMORPH.browser == 'safari') TAPMORPH_SCROLLER.animate({ scrollTop: $(elt).offset().top })
			// else elt.scrollIntoView()
			elt.scrollIntoView()
		},
		body = document.body || document.querySelector('body')
	if (body) body.addEventListener(
		'click',
		event => {
			const elt = event.target
			if (elt.matches('[data-scroll-to]')) {
				const to = elt.getAttribute('data-scroll-to')
				if (to.match(/\d+/)) {
					// the safari check has been removed since its new version now supports: css-scroll-behavior
					// @see https://caniuse.com/css-scroll-behavior
					// if (TAPMORPH.browser == 'safari') scrollToAnimate(to)
					// // if (TAPMORPH.browser == 'safari') TAPMORPH_SCROLLER.animate({ scrollTop: to })
					// else window.scrollTo(0, to)
					window.scrollTo(0, to)
				} else {
					const target = body.querySelector(to)
					if (target) scrollTo(target)
				}
			}
		}
	)

	//handle the form template submission
	const section = document.querySelector(`html.template-form article#${TAPMORPH_CONTENT} > section.${TAPMORPH.add}ajax`)
	if (section) { //TRUE if the form template should be processed via AJAX
		const form = section.querySelector(`form`)
		form.addEventListener(
			'submit',
			/**
			 * @description this function is used to submit the form
			 * @see https://learn.getgrav.org/17/forms/forms/how-to-ajax-submission
			 * @param {Event} event 
			 */
			event => {
				event.preventDefault()
				const
					notif = 'form ~ ' + TAPMORPH_NOTIFICATION,
					alert = section.querySelector(notif + '.' + TAPMORPH_ACTIVE),
					progress = section.querySelector(notif + '-progress').classList,
					mask = section.querySelector(`form ~ .mask`).classList
					;
				if (alert) alert.classList.remove(TAPMORPH_ACTIVE) //previously activated notification
				progress.add(TAPMORPH_ACTIVE)
				mask.add(TAPMORPH_ACTIVE) //prevent any additional changes
				fetch(
					form.getAttribute('action'),
					{
						method: form.getAttribute('method'),
						body: new FormData(form)
					}
				)
					.then(response => {
						if (TAPMORPH.dev) console.log(response, response.text(), response.statusText)

						if (response.ok && response.status == 200) return response.text()
						else throw new Error(response.statusText)
					})
					.then(output => {
						const alert = section.querySelector(notif + '-success')
						if (output) alert.innerHTML = `<span>${alert.firstElementChild.innerHTML}</span><br>${output}`
						progress.remove(TAPMORPH_ACTIVE)
						alert.classList.add(TAPMORPH_ACTIVE)
						scrollTo(alert)
						mask.remove(TAPMORPH_ACTIVE)
						if (!TAPMORPH.dev && section.classList.contains(TAPMORPH.add + 'reset')) form.reset()
					})
					.catch(error => {
						const alert = section.querySelector(notif + '-error')
						if (error) alert.innerHTML = `<span>${alert.firstElementChild.innerHTML}</span><br>${error}`
						progress.remove(TAPMORPH_ACTIVE)
						alert.classList.add(TAPMORPH_ACTIVE)
						scrollTo(alert)
						mask.remove(TAPMORPH_ACTIVE)
						if (TAPMORPH.dev) console.error(error)
					});
			}
		)
	}

	TAPMORPH_REGISTRATION(scrollTo) //registration form handlers
})();

/**
 * @since PM (28.02.2021) this function is used to handle the list of subscribers
 * @see https://en.wikipedia.org/wiki/Mailto
 * @param string prefix
 */
(prefix => {
	const container = document.getElementById(prefix + '_container')
	if (container) {
		let map = {} //emails to cell
		const
			selector = document.getElementById(prefix + '_selector'),
			networker = document.getElementById(prefix + '_networker'),
			remover = document.getElementById(prefix + '_remover'),
			attrText = 'data-text',
			attrEmail = 'data-' + prefix,
			query = 'input[type=checkbox].switcher-icon',
			href = () => {
				networker.setAttribute(
					'href',
					`mailto:?to=&bcc=${Object.keys(map).join(';')}&subject=${networker.getAttribute(attrText)}`
				)
			}
			;

		container.addEventListener(
			'click',
			event => { //update the map & the anchor:href
				const elt = event.target
				if (elt.matches(query)) {
					const email = elt.getAttribute(attrEmail)
					if (elt.checked) map[email] = elt.parentElement.parentElement
					else delete map[email]

					href()
				}
			}
		)
		selector.addEventListener(
			'click',
			_ => { //toggle
				const classList = selector.classList, effective = 'effective'
				if (classList.contains(effective)) {
					map = {} //empty the list
					container.querySelectorAll(query).forEach(checkbox => checkbox.checked = false)
					href()
					classList.remove(effective)
				} else {
					container.querySelectorAll(query).forEach(checkbox => {
						map[checkbox.getAttribute(attrEmail)] = checkbox.parentElement.parentElement //email -> cell
						checkbox.checked = true
					})
					href()
					classList.add(effective)
				}
			}
		)
		remover.addEventListener(
			'click',
			_ => {
				const list = Object.keys(map), cells = Object.values(map)
				if (list.length) {
					const texts = JSON.parse(remover.getAttribute(attrText))
					if (window.confirm(texts[0])) {
						selector.disabled = true
						networker.disabled = true
						remover.disabled = true

						TAPMORPH_POST(
							{ [TAPMORPH.emails]: JSON.stringify(list) },
							(success, success2, text) => {
								if (success && success2) {
									map = {} //empty the list
									href()
									cells.forEach(cell => cell.remove()) //remove the emails from the list
									window.alert(texts[1])
								} else window.alert(text)

								//re-enable the buttons
								selector.disabled = false
								networker.disabled = false
								remover.disabled = false
							}
						)
					}
				} else window.alert(selector.getAttribute(attrText))
			}
		)
	}
})('subscribers');
