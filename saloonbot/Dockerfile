FROM node:20-bookworm-slim

WORKDIR /app

ENV NODE_ENV=production

COPY package.json package-lock.json ./
RUN npm ci --omit=dev

COPY src ./src

# Process cwd = /app → auth_info_baileys & .env paths in code match /app/...
CMD ["node", "src/index.js"]
