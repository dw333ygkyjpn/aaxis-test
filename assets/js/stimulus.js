// @see https://stimulus.hotwired.dev/handbook/installing#using-other-build-systems
import {Application, Controller} from "@hotwired/stimulus" // reference version: Stimulus 3.2.1

window.Stimulus = Application.start()
Stimulus.debug = false

Stimulus.register("api", class extends Controller {
  static targets = ["title", "slug"]
  static values = {
    "url": String
  }

  connect() {
    console.log("Dataset: ", this.element.dataset) // list all data properties of the controller
  }

  slugify() {
    let apiUrl = this.urlValue + '?title=' + this.titleTarget.value
    const slugTarget = this.slugTarget
    fetch(apiUrl)
      .then(function (response) {
        return response.json()
      })
      .then(function (data) {
        slugTarget.value = data.slug
      })
      .catch(function (error) {
        console.log('An error occured. 😞', error);
      })
  }
})
