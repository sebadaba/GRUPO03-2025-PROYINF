{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:

{
  packages = [ ];

  dotenv.disableHint = true;

  languages.php = {
    enable = true;
    version = "8.2";
  };

  services.postgres = {
    enable = true;
    listen_addresses = "*";
    initialDatabases = [
      {
        name = "project";
        schema = ./database/querys/tablas.sql;
      }
    ];
  };
}
