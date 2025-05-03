# -------------------------
# BASE DEPENDENCIES STAGE
# -------------------------
FROM node:20-slim AS deps

RUN apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install --no-install-recommends -y build-essential python3 ca-certificates wget bash \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY package.json package-lock.json* ./

RUN npm ci

# -------------------------
# DEVELOPMENT STAGE
# -------------------------
FROM deps AS development

WORKDIR /app

# Alle Quellen kopieren (achte auf .dockerignore)
COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]

# -------------------------
# BUILD STAGE
# -------------------------
FROM development AS builder

RUN npm run build

# -------------------------
# PRODUCTION STAGE
# -------------------------
FROM nginx:stable-alpine AS production

WORKDIR /usr/share/nginx/html

RUN rm -rf ./*

COPY --from=builder /app/dist ./

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
