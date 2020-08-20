<template>
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manajemen Kategori</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <a href="#">Home</a>
              </li>
              <li class="breadcrumb-item active">Kategori</li>
            </ol>
          </div>
        </div>
      </div>
    </div>​
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <form class="needs-validation" novalidate @submit="confirmCategory">
                <div class="card-body">
                  <div class="form-group">
                    <label for="name">Kategori</label>
                    <input
                      type="text"
                      name="name"
                      class="form-control"
                      id="name"
                      v-model="category.name"
                      required
                    />
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Nama tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea
                      name="description"
                      id="description"
                      cols="5"
                      rows="5"
                      class="form-control"
                      v-model="category.description"
                    ></textarea>
                  </div>
                </div>
                <div class="card-footer">
                  <button v-if="!isEdit" type="submit" class="btn btn-primary">Simpan</button>
                  <div v-else>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <button type="button" class="btn btn-danger" @click="cancelEdit">Batal</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <td>#</td>
                        <td>Kategori</td>
                        <td>Deskripsi</td>
                        <td>Aksi</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(category, key) in categories.data" :key="key">
                        <td>{{ key + 1 }}</td>
                        <td>{{ category.name }}</td>
                        <td>{{ category.description }}</td>
                        <td>
                          <button class="btn btn-warning btn-sm" @click="editCategory(category)">
                            <i class="fa fa-edit"></i>
                          </button>
                          <button class="btn btn-danger btn-sm" @click="confirmDelete(category.id)">
                            <i class="fa fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                      <tr v-if="categories.data.length == 0">
                        <td colspan="4" class="text-center">Tidak ada data</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      isEdit: false,
      category: {
        id: "",
        name: "",
        description: "",
      },
      categories: {
        data: [],
      },
    };
  },
  computed: {
    ...mapState("user", {
      user: (state) => state.user,
    }),
  },
  created() {
    this.fetchCategories();
  },
  methods: {
    /**
     * ambil data kategori dari rest api
     */
    async fetchCategories() {
      try {
        let endpoint = `${BASE_URL}/category`;
        let response = await axios.get(endpoint);

        if (response.data.status == "success") {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.log(error);
      }
    },
    /**
     * validasi form kategori
     *
     * @param event
     */
    confirmCategory(event) {
      event.preventDefault();
      if (this.category.name.trim() == "") {
        event.target.classList.add("was-validated");
        return;
      }

      Swal.fire({
        title: "Apakah anda yakin?",
        text:
          "Anda akan" + this.isEdit
            ? `memperbaruhi kategori ${this.category.name}`
            : `membuat kategori ${this.category.name}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: this.isEdit ? "Update" : "Create",
      }).then((result) => {
        if (result.value) {
          if (this.isEdit) {
            this.updateCategory();
          } else {
            this.createCategory();
          }
        }
      });
    },
    /**
     * buat kategori baru
     */
    async createCategory() {
      try {
        let endpoint = `${BASE_URL}/category`;
        let body = {
          name: this.category.name,
          description: this.category.description,
        };
        let response = await axios.post(endpoint, body);

        if (response.data.status == "success") {
          console.log("category baru");
          this.fetchCategories();
          this.resetForm();
          Swal.fire("Berhasil!", "Data berhasil disimpan.", "success");
        }
      } catch (error) {
        console.log(error);
        Swal.fire("Gagal!", "Data gagal disimpan.", "error");
      }
    },
    /**
     * mereset inputan form
     */
    resetForm() {
      this.isEdit = false;
      this.category.id = "";
      this.category.name = "";
      this.category.description = "";
    },
    /**
     * beralih ke mode edit
     *
     * @param category
     */
    editCategory(category) {
      this.isEdit = true;
      this.category = { ...category };
    },
    /**
     * membatalkan aksi edit
     */
    cancelEdit() {
      this.isEdit = false;
      this.resetForm();
    },
    /**
     * Memperbarui kategori
     */
    async updateCategory() {
      try {
        let endpoint = `${BASE_URL}/category/${this.category.id}`;
        let response = await axios.put(endpoint, this.category);

        if (response.data.status == "success") {
          this.resetForm();
          this.fetchCategories();
          Swal.fire("Berhasil!", "Data berhasil diperbarui.", "success");
        }
      } catch (error) {
        console.log(error);
        Swal.fire("Gagal!", "Data gagal diperbarui.", "error");
      }
    },
    /**
     * Validasi sebelum menghapus kategori
     *
     * @param id kategori
     */
    confirmDelete(id) {
      Swal.fire({
        title: "Apakah anda yakin?",
        text: "Anda tidak akan dapat mengembalikan ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Delete",
      }).then((result) => {
        if (result.value) {
          this.deleteCategory(id);
        }
      });
    },
    /**
     * Hapus kategori
     *
     * @param id kategori
     */
    deleteCategory(id) {
      try {
        let endpoint = `${BASE_URL}/category/${id}`;
        axios
          .delete(endpoint)
          .then((response) => {
            Swal.fire("Berhasil!", "Data berhasil dihapus.", "success");
            this.fetchCategories();
          })
          .catch((err) => {
            console.log(err);
            Swal.fire("Gagal!", "Data gagal dihapus.", "error");
          });
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style>
</style>
