#include <phpcpp.h>
#include "snow.h"

extern "C" {
PHPCPP_EXPORT void *get_module() {
    static Php::Extension myExtension("snowflake", "1.0");

    Php::Class<SnowFlake> snow("SnowFlake", Php::Final);
    snow.method<&SnowFlake::__construct>("__construct", {
            Php::ByVal("workId", Php::Type::Numeric, true)
    });
    snow.method<&SnowFlake::gen_id>("genId");
    
    Php::Namespace cxNamespace("Cx");

    cxNamespace.add(snow);

    myExtension.add(std::move(cxNamespace));

    return myExtension.module();
}
}
