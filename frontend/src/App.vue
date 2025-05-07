<template>
  <div>
    <AppHeader
      v-if="user"
      :user="user"
    />
    <div class="container mt-3">
      <div class="row">
        <div class="col">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppHeader from "./components/AppHeader.vue";
import {getCurrentUser} from "./composables/CurrentUser.js";
import {isAuthenticated} from "./services/auth.js";

export default {
  name: 'App',
  components: {AppHeader},
  setup() {
    let user = null;

    if (isAuthenticated()) {
      user = getCurrentUser().user
    }

    return {user}
  }
}
</script>
