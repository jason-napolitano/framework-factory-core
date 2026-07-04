# Framework Factory

> Framework Factory is a PHP application starter package. The aim is to make building PHP 8 powered applications (and
> frameworks) a breeze by giving a construct for the beginning stages of application development. The core principles
> are inspired by the Laravel bootstrap architecture: wherein a developer can hook into the service container before,
> during and after container and dependency resolutions have occurred.
>
> Using a simple yet intuitive bootstrap process and application endpoint interface, developers have access to the
> logic and tools needed to get their next project going with minimal overhead. Check out the features list below for a
> more comprehensive look into how Framework Factory is able to help achieve this.

## Features

- **Application endpoint:** The application endpoint is located at `FrameworkFactory\Application`. This class
  assists in managing an application by providing an elegant boostrap and application configuration API.
- **PSR-11 IoC Container:** The IoC container is a robust container that includes many features for managing
  dependencies within an application, as well as providing access to them.
    - **Container Features**:
        - **Container lifecycle hooks:** Lifecycle hooks allow developers to interact with the container at different
          stages of execution.
        - **Context binding support:** Contextual rules allow for dependencies to be _swapped_ out when a certain set of
          circumstances are met.
        - **Provider caching:** Load service providers from a cache file for faster and easier retrieval.
        - **Facade support:** - Access dependencies loaded within the container using Facades (Accessors) for easy
          resource consumption.
        - **Lazy loading:** Load dependencies only when they are needed, instead of when a container is built.
- **Built-in autoloader:** The built-in autoloader allows developers to configure the application namespace from within
  the application itself, instead of the `composer.json` file; allowing multiple application instances to use
  their own namespaces.
- **Provider Auto-discovery:** Using the built-in autoloader, developers can auto-discover any service providers that
  live within a configurable namespace.
- **Fully tested codebase:** The codebase is fully tested using the [Pest PHP](https://pestphp.com/) testing framework.
  You can see all tests by going [here](https://github.com/FrameworkFactoryPHP/core/tree/main/tests).
- **Zero Dependencies:** Besides `psr/container`, FrameworkFactory requires zero dependencies and relies strictly on
  its own internal libraries.
- **Fully Documented:** All documentation is provided in the separate
  `docs` [ repository](https://github.com/FrameworkFactoryPHP/docs).

___

## License

BSD 3-Clause License

Copyright (c) 2026, Framework-Factory

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution.

3. Neither the name of the copyright holder nor the names of its
   contributors may be used to endorse or promote products derived from
   this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.