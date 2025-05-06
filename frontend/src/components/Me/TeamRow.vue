<template>
  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex align-items-center">
        <div class="flex-fill">
          <h5 class="mb-0 pb-0">
            {{ team.name }}
          </h5>
          <div class="small text-muted">
            /{{ team.slug }}
          </div>
        </div>
        <div class="flex-column">
          <a
            href="#"
            class="btn btn-sm btn-outline-primary me-1"
          >
            <font-awesome-icon icon="pencil" />
            {{ $t('teams.buttons.edit') }}
          </a>
          <a
            href="#"
            class="btn btn-sm btn-outline-danger"
          >
            <font-awesome-icon icon="trash" />
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {Team} from "../../types/Team";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

defineProps<{ team: Team }>()
</script>
