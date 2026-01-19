const button = document.getElementById('toggleButton');
const content = document.getElementById('contentToHide');

button.addEventListener('click', () => {
  content.classList.toggle('hidden');
});
