document.getElementById('registerForm').addEventListener('submit', function(event) {
   event.preventDefault();

   const password = document.getElementById('password').value;
   const confirmPassword = document.getElementById('confirm_password').value;
   const errorMessage = document.getElementById('error-message');

   if (password !== confirmPassword) {
       errorMessage.textContent = 'Las contraseñas no coinciden.';
       errorMessage.classList.remove('d-none');
       return;
   }

   // Aquí puedes agregar la lógica para enviar los datos a tu servidor para registrar al usuario

   // Si el registro es exitoso, puedes redirigir al usuario o mostrar un mensaje de éxito
   alert('Registro exitoso');
   window.location.href = 'index.html'; // Redirige al inicio de sesión
});

