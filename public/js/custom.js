const button = document.getElementById('toggleButton');
const content = document.getElementById('contentToHide');

if (button) {
  button.addEventListener('click', () => {
    content.classList.toggle('hidden');
  });
}
