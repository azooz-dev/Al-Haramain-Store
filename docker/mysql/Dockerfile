FROM mysql:8.0

# Set environment variables for MySQL 8.0 authentication
ENV MYSQL_ROOT_PASSWORD=temp_password
ENV MYSQL_DATABASE=railway

# Configure MySQL to use mysql_native_password by default
RUN echo "[mysqld]" > /etc/mysql/conf.d/auth.cnf \
  && echo "default-authentication-plugin=mysql_native_password" >> /etc/mysql/conf.d/auth.cnf

EXPOSE 3306