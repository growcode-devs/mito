const galleryContainer = document.querySelector(".gallery-container");
const galleryControlsContainer = document.querySelector(".gallery-controls");
const galleryControls = ["previous", "next"];
const galleryItems = document.querySelectorAll(".gallery-item");

class Carousel {
  constructor(container, items, controls) {
    this.carouselContainer = container;
    this.carouselControls = controls;
    this.carouselArray = [...items];
  }

  updateGallery() {
    this.carouselArray.forEach((el) => {
      el.classList.remove("gallery-item-1");
      el.classList.remove("gallery-item-2");
      el.classList.remove("gallery-item-3");
      //   el.classList.remove("gallery-item-4");
      //   el.classList.remove("gallery-item-5");
    });
    const dots = document.querySelectorAll(".dot");
    this.carouselArray.slice(0, 3).forEach((el, i) => {
      el.classList.add(`gallery-item-${i + 1}`);
      if (i == 1) {
        dots.forEach((d) => {
          if (d.dataset.item == el.dataset.item) {
            d.classList.add("active");
          } else {
            d.classList.remove("active");
          }
        });
      }
    });
  }

  setCurrentState(direction) {
    if (direction.className == "gallery-controls-previous") {
      this.carouselArray.unshift(this.carouselArray.pop());
    } else {
      this.carouselArray.push(this.carouselArray.shift());
    }
    this.updateGallery();
  }

  //   setControls() {
  //     this.carouselControls.forEach((control) => {
  //       galleryControlsContainer.appendChild(
  //         document.createElement("button")
  //       ).className = `gallery-controls-${control}`;
  //       document.querySelector(`.gallery-controls-${control}`).innerText =
  //         control;
  //     });
  //   }

  useControls() {
    // const triggers = [...galleryControlsContainer.childNodes];
    const triggers = document.querySelectorAll(".gallery-controls button");

    triggers.forEach((control) => {
      control.addEventListener("click", (e) => {
        e.preventDefault();
        this.setCurrentState(control);
      });
    });
  }
  useDotControls() {
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot) => {
      dot.addEventListener("click", (e) => {
        e.preventDefault();
        if (
          Number(this.carouselArray[1].dataset.item) + 1 ==
            Number(dot.dataset.item) ||
          (this.carouselArray[1].dataset.item == 3 && dot.dataset.item == 1)
        ) {
          this.setCurrentState(
            document.querySelector(".gallery-controls-next")
          );
        } else if (this.carouselArray[1].dataset.item == dot.dataset.item) {
        } else {
          this.setCurrentState(
            document.querySelector(".gallery-controls-previous")
          );
        }
      });
    });
  }
}

const exampleCarousel = new Carousel(
  galleryContainer,
  galleryItems,
  galleryControls
);
// exampleCarousel.setControls();
exampleCarousel.useControls();
exampleCarousel.useDotControls();
