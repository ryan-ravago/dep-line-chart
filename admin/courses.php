<?php require_once './assets/php/header.php'; ?>
<main>
  <div class="container-fluid">
    <button data-bs-toggle="modal" data-bs-target="#add-course-modal" class="float-end btn btn-success">Add Course</button>
    <br><br>

    <!-- Add Party Modal -->
    <div class="modal fade" id="add-course-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h1 class="modal-title fs-5">Add course</h1>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="add-course-form">
              <label class="form-label">Course name:</label>
              <input type="text" name="course-name" id="course-name" class="form-control mb-3" required>


              <label class="form-label">Course Description:</label>
              <input type="text" name="course-desc" id="course-desc" class="form-control mb-3" required>

              <input type="submit" value="Add Course" id="add-course-btn" class="btn btn-success w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Edit Party Modal -->
    <div class="modal fade" id="edit-course-modal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h1 class="modal-title fs-5">Edit course</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-course-form">
              <input type="hidden" name="edit-course-id" id="edit-course-id">
              <label class="form-label">Course name:</label>
              <input type="text" name="edit-course-name" id="edit-course-name" class="form-control mb-3" required>

              <label class="form-label">Course Description:</label>
              <input type="text" name="edit-course-desc" id="edit-course-desc" class="form-control mb-3" required>

              <input type="submit" value="Update Course" id="update-course-btn" class="btn btn-warning w-100 mt-3">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card rounded-0 shadow-sm mt-3">
          <div class="card-header bg-success rounded-0">
            <span class="fs-5 text-white">Courses</span>
          </div>
          <div class="card-body rounded-0 table-responsive" id="data-wrapper">
            <div class="d-flex align-items-center justify-content-center">
              <div class="spinner-border text-secondary" role="status"></div>
              <h2 class="text-secondary ms-2">Loading...</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once './assets/php/footer.php'; ?>
<script>
  $(document).ready(function() {
    // SweetAlert2
    function swal(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
      })
    }

    // Add Course
    $('#add-course-btn').click(function(e) {
      if ($('#add-course-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Adding Course...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#add-course-form').serialize() + '&action=addCourse',
          success: res => {
            if (res == '1') {
              fetchCourses()
              swal('success', 'Added!', 'Course was successfully added.')
              $('#add-course-form')[0].reset()
              $('#add-course-modal').modal('hide')
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }
            $('#add-course-btn').val('Add Course')
            $('#add-course-btn').prop('disabled', false)
          }
        })
      }
    })

    // Fetch Courses
    function fetchCourses() {
      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: { action: 'fetchCourses' },
        success: res => {
          console.log(res)
          let courses = JSON.parse(res)
          console.log(courses)

          if (courses.length < 1) {
            $("#data-wrapper").html(`
              <h4 class="text-center text-secondary fst-italic">No Courses.</h4>
            `)
            return;
          }

          let coursesMapped = courses.map(course => `
            <tr>
              <td>${course.course_name}</td>
              <td>${course.course_desc}</td>
              <td>
                <a href="#" title="Edit" class="edit-course text-decoration-none" id="edit-course-${course.course_id}" data-bs-toggle="modal" data-bs-target="#edit-course-modal">
                  <i class="bi bi-pencil-square text-warning fs-5"></i>
                </a>
                <a href="#" title="Delete" class="del-course text-decoration-none" id="del-course-${course.course_id}">
                  <i class="bi bi-trash-fill text-danger fs-5"></i>
                </a>
              </td>
            </tr>
          `)
          let coursesJoined = coursesMapped.join('')
          let courseTable = `
            <table class="table table-striped table-bordered w-100" id="courses-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                ${coursesJoined}
              </tbody>
            </table>
          `
          $('#data-wrapper').html(courseTable)
          // *******
          // *******
          // Setup - add a text input to each footer cell
          $('#courses-table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#courses-table thead');
      
          var table = $('#courses-table').DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            initComplete: function () {
              var api = this.api();

              // For each column
              api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                  // Set the header cell to contain the input element
                  var cell = $('.filters th').eq(
                      $(api.column(colIdx).header()).index()
                  );
                  var title = $(cell).text();
                  $(cell).html(`<input type="text" placeholder="${title}" class="form-control form-control-sm ${title == 'Actions' ? 'd-none' : ''}" />`);

                  // On every keypress in this input
                  $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                    .off('keyup change')
                    .on('change', function (e) {
                      // Get the search value
                      $(this).attr('title', $(this).val());
                      var regexr = '({search})'; //$(this).parents('th').find('select').val();

                      var cursorPosition = this.selectionStart;
                      // Search the column for that value
                      api
                        .column(colIdx)
                        .search(
                          this.value != ''
                              ? regexr.replace('{search}', '(((' + this.value + ')))')
                              : '',
                          this.value != '',
                          this.value == ''
                        )
                        .draw();
                    })
                    .on('keyup', function (e) {
                      e.stopPropagation();

                      $(this).trigger('change');
                      $(this)
                        .focus()[0]
                        // .setSelectionRange(cursorPosition, cursorPosition);
                    });
                });
            },
          });
        }
      })
    }
    fetchCourses()

    // Edit Course
    $('body').on('click', '.edit-course', function(e) {
      e.stopPropagation()

      let id = $(this).attr('id')
      id = id.substr(12) 

      $.ajax({
        url: './assets/php/action.php',
        method: 'post',
        data: {
          action: 'fetchCourse',
          id
        },
        success: res => {
          let course = JSON.parse(res)
          
          $('#edit-course-id').val(course.course_id)
          $('#edit-course-name').val(course.course_name)
          $('#edit-course-desc').val(course.course_desc)
        }
      })
    })

    // Update Course
    $("#update-course-btn").click(function(e) {
      if ($('#edit-course-form')[0].checkValidity()) {
        e.preventDefault()

        $(this).val('Updating Course...')
        $(this).prop('disabled', true)

        $.ajax({
          url: './assets/php/action.php',
          method: 'post',
          data: $('#edit-course-form').serialize() + '&action=updateCourse',
          success: res => {
            console.log(res)

            if (res == '1') {
              fetchCourses()
              swal('success', 'Updated!', 'Course successfully updated!')
              $('#edit-course-form')[0].reset()
              $('#edit-course-modal').modal('hide')
            } else {
              swal('error', 'Oops!', 'Something went wrong, try again.')
            }

            $("#update-course-btn").val('Update Course')
            $("#update-course-btn").prop('disabled', false)
          }
        })
      }
    })

    // Delete Course
    $('body').on('click', '.del-course', function(e) {
      let id = $(this).attr('id')
      id = id.substr(11)
     
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: './assets/php/action.php',
            method: 'post',
            data: { action: 'delCourse', id },
            success: function(res) {
              console.log(res)
              if (res == '1') {
                fetchCourses()
                swal('success', 'Deleted!', 'Course was successfully deleted.')
              }  else {
                swal('error', 'Oops!', 'Something went wrong, try again.')
              }
            }
          })
        }
      })
    })
  })
</script>