{ pkgs, ... }: {
  # Add packages to your environment
  packages = [
    pkgs.php83
  ];

  # Preview configuration for basic HTML files if needed
  previews = {
    server = {
      # A simple command to serve static files
      command = ["npx", "-y", "http-server", "-p", "$PORT", "--cors"];
      port = 8080;
    };
  };
}
