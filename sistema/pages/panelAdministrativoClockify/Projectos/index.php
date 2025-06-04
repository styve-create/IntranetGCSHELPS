
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fc;
    }

    .project-table th, .project-table td {
      vertical-align: middle;
    }
    .dot {
      height: 10px;
      width: 10px;
      background-color: #9b59b6;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
    .color-box {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
}

.color-box.add-color {
  background-color: #fff;
}
  </style>

    <div id="contenido-principal">
        <div class="container-fluid">
  <div class="row">

    <!-- Main content -->
    <div class="col-10 py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Projects hola</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
  Create New Project
</button>
      </div>

      <!-- Filters -->
      <div class="row mb-3 g-2" style="background-color: white;">
        <div class="col">
          <select class="form-select">
            <option selected>Active</option>
            <option>Archived</option>
          </select>
        </div>
        <div class="col">
          <select class="form-select">
            <option selected>Client</option>
          </select>
        </div>
        <div class="col">
          <select class="form-select">
            <option selected>Billing</option>
          </select>
        </div>
        <div class="col-4">
          <input id="searchInput" type="text" class="form-control" placeholder="Search project..." />
        </div>
        <div class="col-auto">
          <button class="btn btn-outline-primary">Apply Filter</button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-bordered project-table">
          <thead class="table-light">
            <tr>
              <th scope="col"><input type="checkbox" /></th>
              <th scope="col">Name</th>
              <th scope="col">Client</th>
              <th scope="col">Tracked</th>
              <th scope="col">Amount</th>
              <th scope="col">Progress</th>
              <th scope="col">Access</th>
              <th scope="col">⭐</th>
              <th scope="col">⋮</th>
            </tr>
          </thead>
          <tbody id="projectTableBody">
            <tr>
              <td><input type="checkbox" /></td>
              <td><span class="dot"></span> <strong>Hola3</strong></td>
              <td>global</td>
              <td>0,00h</td>
              <td>-</td>
              <td>-</td>
              <td>Public</td>
              <td><i class="bi bi-star"></i></td>
              <td><i class="bi bi-three-dots-vertical"></i></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Crear Nuevo Proyecto -->
<div class="modal fade" id="modalNuevoProyecto" tabindex="-1" aria-labelledby="modalNuevoProyectoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-4">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoProyectoLabel">Create new Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="formNuevoProyecto">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <input type="text" class="form-control" placeholder="Enter Project name" required>
            </div>
            <div class="col-md-6">
              <select class="form-select">
                <option selected>Select client</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-center gap-2">
     <div class="dropdown">
  <div class="btn-group">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    Dropdown
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <li><div class="color-box" data-color="#e74c3c" style="background-color:#e74c3c;"></div></li>
      <li><div class="color-box" data-color="#9b59b6" style="background-color:#9b59b6;"></div></li>
      <li><div class="color-box" data-color="#2980b9" style="background-color:#2980b9;"></div></li>
      <li><div class="color-box" data-color="#3498db" style="background-color:#3498db;"></div></li>
      <li><div class="color-box" data-color="#1abc9c" style="background-color:#1abc9c;"></div></li>
      <li><div class="color-box" data-color="#2ecc71" style="background-color:#2ecc71;"></div></li>
      <li><div class="color-box" data-color="#e67e22" style="background-color:#e67e22;"></div></li>
      <li><div class="color-box" data-color="#8d6e63" style="background-color:#8d6e63;"></div></li>
  </ul>
</div>
</div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Public</label>
              </div>
            </div>
            <div class="col-md-6">
              <select class="form-select">
                <option selected>No template</option>
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">CREATE</button>
      </div>
    </div>
  </div>
</div>

<!-- Selector de color oculto -->
<input type="color" id="colorPersonalizado" style="display: none;">
    </div>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
      (function(){ 

  document.getElementById("searchInput").addEventListener("keyup", function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll("#projectTableBody tr").forEach(row => {
      const name = row.cells[1].textContent.toLowerCase();
      row.style.display = name.includes(query) ? "" : "none";
    });
  });
  
document.addEventListener('click', function (e) {
  const target = e.target;

  if (target.classList.contains('color-box')) {
    const colorPreview = document.getElementById("colorDropdown");
    const colorPicker = document.getElementById("colorPersonalizado");

    if (target.classList.contains('add-color')) {
      colorPicker.click();
    } else {
      const color = target.dataset.color;
      colorPreview.style.backgroundColor = color;
    }
  }
});

document.getElementById("colorPersonalizado").addEventListener("input", function () {
  document.getElementById("colorDropdown").style.backgroundColor = this.value;
});
      })();

</script>

