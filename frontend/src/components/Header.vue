<template>
  <nav class="navbar navbar-expand-lg bg-light sticky-top">
    <div class="container-fluid">
      <router-link
        class="navbar-brand"
        to="/dashboard"
      >
        OpenCal
      </router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon" />
      </button>
      <div
        id="navbarSupportedContent"
        class="collapse navbar-collapse"
      >
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link
              class="nav-link"
              to="/dashboard"
              active-class="active"
            >
              {{ $t('header.nav.dashboard') }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              class="nav-link"
              to="/appointments"
              active-class="active"
            >
              {{ $t('header.nav.appointments') }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              to="/logout"
              class="nav-link"
            >
              <font-awesome-icon icon="sign-out" />
              {{ $t('header.nav.logout') }}
            </router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
</script>
