<?php
require_once "../class/webstoreclass.php";
$user = $store->get_userdata();
$title = "Inventory - Products";
include_once "../includes/dashboard_header.php";
$admins = $store->get_admin();
$products = $store->get_products();
?>
  <body id="page-top">
    <?php include_once "../includes/preloader.php"; ?>
    <?php
    $store->add_inventory_products();
    $addedInventoryProducts = $store->get_inventory_products_added();
    $countOrders = $store->count_orders();
    $pendingOrders = $store->get_pending_orders();
    ?>
    <!-- Page Wrapper -->
    <div id="wrapper">
      <?php include_once "../includes/dashboard_sidebar.php"; ?>
      <!-- Content Wrapper -->
      <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
          <?php include_once "../includes/dashboard_navbar.php"; ?>
          <!-- Begin Page Content -->
          <div class="container-fluid">
            <!-- Add Inventory Products Modal Form -->
            <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form method="post" id="inventoryProductsForm">
                      <input type="hidden" name="stockID" id="stockID">
                      <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="addedStockQty" class="form-control" min="1" value="1">
                      </div>
                      <div class="form-group">
                        <label>Product</label>
                        <select class="selectpicker show-tick form-control" name="products" id="productsForm" data-live-search="true">
                          <option selected disabled>Select Product</option>
                          <?php foreach ($products as $product) { ?>
                            <?php $stocks = $store->view_all_stocks(
                              $product["ID"]
                            ); ?>
                            <?php foreach ($stocks as $stock) { ?>
                              <?php if ($stock["sku"]) { ?>
                                <option value="<?= $product[
                                  "ID"
                                ] ?>" data-id="<?= $stock[
  "ID"
] ?>" data-tokens="<?= $product["productName"] ?>"><?= is_null($stock["size"])
  ? $product["productName"] . " (" . $stock["variantName"] . ")"
  : $product["productName"] .
    " (" .
    $stock["variantName"] .
    ") (" .
    $stock["size"] .
    ")" ?></option>
                              <?php } ?>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="addInventoryProducts" class="btn btn-secondary" form="inventoryProductsForm">Add</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of Modal Form -->
            <!-- Page Heading -->
            <div
              class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none"
            >
              <h1 class="h3 mb-4 text-gray-800">Finished Products
                <button type="button" href="#" class="btn btn-secondary btn-icon-split" data-toggle="modal" data-target="#productModal">
                    <span class="icon text">
                      <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Add Inventory</span>
                </button>
              </h1>
              <div class="dropdown no-arrow mb-4">
                <button class="btn btn-info btn-sm shadow-sm dropdown-toggle" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-download fa-sm text"></i>
                    Generate Report
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a onclick="window.print()" class="dropdown-item" href="#">Print</a>
                  <a class="dropdown-item" href="javascript:genPDF()">Export to PDF</a>
                </div>
              </div>
            </div>
            <!-- print page -->
            <div class="d-none d-print-block">
              <div class="d-sm-flex align-items-center justify-content-between m-4">
                <h1 id="heading" class="m-0 font-weight-bold text-gray-800">Inventory - Product<br/>List</h1>
                <img id="img" class="m-4" src="./assets/img/logo.png" alt="" width="150px">
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table
                    class="table table-striped text-center"
                    width="100%"
                    id="table"
                    cellspacing="0"
                  >
                    <thead class="text-info">
                        <tr>
                          <th>#</th>
                          <th>Product Name</th>
                          <th>Size</th>
                          <th>Added Stock</th>
                          <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900">
                      <?php if ($addedInventoryProducts) { ?>
                        <?php foreach (
                          $addedInventoryProducts
                          as $inventoryProduct
                        ) { ?>
                          <tr>
                            <td><?= $inventoryProduct["ID"] ?></td>
                            <td><?= $inventoryProduct["productName"] .
                              " (" .
                              $inventoryProduct["variantName"] .
                              ")" ?></td>
                            <td><?= $inventoryProduct["size"]
                              ? $inventoryProduct["size"]
                              : "n/a" ?></td>
                            <td><?= $inventoryProduct["addedQty"] ?></td>
                            <td><?= $inventoryProduct["dateTime"] ?></td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                    <tfoot class="text-info">
                        <tr>
                          <th>#</th>
                          <th>Product Name</th>
                          <th>Size</th>
                          <th>Added Stock</th>
                          <th>Date & Time</th>
                        </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="card-body">
                <h6 id="issuedBy" class="m-0 text-gray-800">ISSUED BY: <?= $user[
                  "firstName"
                ] .
                  " " .
                  $user["lastName"] ?></h6>
                <h6 id="issuedDate" class="m-0 text-gray-800">DATE: <span id="date"></span></h6>
              </div>
            </div>
            <!-- Inventory Products Table -->
            <div class="card shadow mb-4 d-print-none">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-gray-800">
                  Finished Products List
                </h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table
                    class="table table-striped text-center"
                    id="dataTable"
                    width="100%"
                    cellspacing="0"
                    data-order='[[ 0, "desc" ]]'
                  >
                    <thead class="bg-gray-600 text-gray-100">
                        <tr>
                          <th>#</th>
                          <th>Product Name</th>
                          <th>Size</th>
                          <th>Added Stock</th>
                          <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900">
                      <?php if ($addedInventoryProducts) { ?>
                        <?php foreach (
                          $addedInventoryProducts
                          as $inventoryProduct
                        ) { ?>
                          <tr>
                            <td><?= $inventoryProduct["ID"] ?></td>
                            <td><?= $inventoryProduct["productName"] .
                              " (" .
                              $inventoryProduct["variantName"] .
                              ")" ?></td>
                            <td><?= $inventoryProduct["size"]
                              ? $inventoryProduct["size"]
                              : "n/a" ?></td>
                            <td><?= $inventoryProduct["addedQty"] ?></td>
                            <td><?= $inventoryProduct["dateTime"] ?></td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                    <tfoot class="bg-gray-600 text-gray-100">
                        <tr>
                          <th>#</th>
                          <th>Product Name</th>
                          <th>Size</th>
                          <th>Added Stock</th>
                          <th>Date & Time</th>
                        </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
        <?php require_once "../includes/dashboard_footer.php"; ?>
      </div>
      <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
    <?php require_once "../includes/dashboard_scripts.php"; ?>
    <script>
      const productsForm = document.querySelector("#productsForm");

      if (productsForm) {
        productsForm.addEventListener("change", () => {
          let stockID = productsForm.options[productsForm.selectedIndex].dataset.id;

          document.querySelector("#stockID").value = stockID;
        });
      }
    </script>
    <script>
      const setDate = new Date();
      const date = setDate.toDateString();
      const time = setDate.toLocaleTimeString();

      document.querySelector('#date').innerHTML = date + ' ' + time;
    </script>
    <script>
      function genPDF(){
        const heading = document.querySelector('#heading');
        const img = document.querySelector('#img');
        const issuedBy = document.querySelector('#issuedBy');
        const date = document.querySelector('#issuedDate');
        const doc = new jsPDF('p', 'pt', 'letter');
        const elementHandler = {
          '#ignorePDF': function (element, renderer) {
            return true;
          }
        };
        doc.fromHTML(heading,40,60,{
          'width': 522,'elementHandlers': elementHandler
        });
        doc.addImage(img, 'PNG', 420,40,150,90);
        doc.autoTable({html: '#table', margin: { top: 160 }, headStyles: { fillColor: [84, 84, 84]}, footStyles: {fillColor: [84, 84, 84]}, styles: { halign: 'center'}});
        doc.fromHTML(issuedBy,40,650,{
          'width': 522,'elementHandlers': elementHandler
        });
        doc.fromHTML(date,40,670,{
          'width': 522,'elementHandlers': elementHandler
        });
        doc.save("Inventory - Product List.pdf");
      }
    </script>
  </body>
</html>


