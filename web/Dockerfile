FROM node:20.11.0-slim 
WORKDIR /weather-web
COPY . .
RUN rm -rf node_modules
RUN npm install
CMD ["npm", "run", "dev"]
EXPOSE 5173

