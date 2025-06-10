FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the app
COPY . .

# Build the app
RUN npm run build

# Install serve to run the application
RUN npm install -g serve

# Expose port for the app
EXPOSE 5173

# Start the app
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
