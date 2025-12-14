let carrito = [];

function renderCarrito() {
  const tbody = document.querySelector('#tablaCarrito tbody');
  tbody.innerHTML = '';
  let total = 0;

  carrito.forEach((item, index) => {
    const subtotal = item.cantidad * item.precio;
    total += subtotal;

    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td>${item.nombre}</td>
      <td>
        <input type="number" min="1" class="form-control form-control-sm"
               value="${item.cantidad}"
               onchange="cambiarCantidad(${index}, this.value)">
      </td>
      <td>$${item.precio.toFixed(2)}</td>
      <td>$${subtotal.toFixed(2)}</td>
      <td>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarItem(${index})">X</button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  document.getElementById('totalGeneral').textContent = '$' + total.toFixed(2);
  document.getElementById('total_input').value = total.toFixed(2);
}

function agregarAlCarrito(id, nombre, precio) {
  precio = parseFloat(precio);
  const index = carrito.findIndex(i => i.id === id);
  if (index >= 0) {
    carrito[index].cantidad += 1;
  } else {
    carrito.push({ id, nombre, precio, cantidad: 1 });
  }
  renderCarrito();
}

function cambiarCantidad(index, value) {
  const cantidad = parseInt(value) || 1;
  carrito[index].cantidad = cantidad > 0 ? cantidad : 1;
  renderCarrito();
}

function eliminarItem(index) {
  carrito.splice(index, 1);
  renderCarrito();
}

function prepararEnvioVenta() {
  if (carrito.length === 0) {
    alert('No hay productos en el carrito.');
    return false;
  }
  document.getElementById('cart_json').value = JSON.stringify(carrito);
  return true;
}

// Eventos de botones "Agregar"
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-agregar').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id);
      const nombre = btn.dataset.nombre;
      const precio = parseFloat(btn.dataset.precio);
      agregarAlCarrito(id, nombre, precio);
    });
  });

  // Buscador de productos
  const buscador = document.getElementById('buscadorProductos');
  const tabla = document.getElementById('tablaProductos');

  if (buscador && tabla) {
    buscador.addEventListener('keyup', () => {
      const filtro = buscador.value.toLowerCase();
      const filas = tabla.querySelectorAll('tbody tr');
      filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
      });
    });
  }
});
